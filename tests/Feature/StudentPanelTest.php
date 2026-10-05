<?php

namespace Tests\Feature;

use App\Http\Middleware\StudentMiddleware;
use App\Livewire\StudentPanel\Auth\LoginForm;
use App\Livewire\StudentPanel\Pages\Profile\Parts\ProfileForm;
use App\Livewire\StudentPanel\Parts\StudentProfileCard;
use Database\Factories\AdminFactory;
use Database\Factories\BranchFactory;
use Database\Factories\StudentFactory;
use Database\Factories\StudentProfileFactory;
use Database\Factories\TeacherFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StudentPanelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Attendance is outside this panel's scope and its existing migration defines duplicate columns.
     *
     * @return array<string, mixed>
     */
    protected function migrateFreshUsing(): array
    {
        return [
            '--path' => collect(glob(database_path('migrations/*.php')))
                ->reject(fn (string $path) => str_contains($path, 'create_attendances_table'))
                ->values()->all(),
            '--realpath' => true,
        ];
    }

    public function test_student_login_page_renders_with_its_own_assets_and_rtl(): void
    {
        $this->get(route('student.login'))
            ->assertOk()
            ->assertSee('lang="ar" dir="rtl"', false)
            ->assertSee('assets/studentPanel/images/logo.png')
            ->assertSeeLivewire(LoginForm::class)
            ->assertDontSee('assets/teacherPanel');
    }

    #[DataProvider('credentialFields')]
    public function test_active_students_can_log_in_with_teacher_style_credentials(string $field): void
    {
        $student = StudentFactory::new()->create();
        $sessionId = session()->getId();

        Livewire::test(LoginForm::class)
            ->set('login', $student->{$field})
            ->set('password', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($student, 'student');
        $this->assertGuest('teacher');
        $this->assertGuest('admin');
        $this->assertNotSame($sessionId, session()->getId());
    }

    public static function credentialFields(): array
    {
        return [['username'], ['email'], ['phone']];
    }

    public function test_invalid_credentials_and_inactive_students_are_rejected(): void
    {
        $student = StudentFactory::new()->create();
        $inactive = StudentFactory::new()->inactive()->create();

        foreach ([[$student->email, 'incorrect'], [$inactive->email, 'password'], ['unknown@example.com', 'password']] as [$login, $password]) {
            Livewire::test(LoginForm::class)
                ->set('login', $login)
                ->set('password', $password)
                ->call('submit')
                ->assertHasErrors('login')
                ->assertSet('password', '');
            $this->assertGuest('student');
        }
    }

    public function test_login_requires_credentials_and_throttles_failed_attempts(): void
    {
        Livewire::test(LoginForm::class)->call('submit')->assertHasErrors(['login', 'password']);

        $component = Livewire::test(LoginForm::class)->set('login', 'unknown@example.com');
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $component->set('password', 'incorrect')->call('submit')->assertHasErrors('login');
        }
        $component->assertSee('محاولات كثيرة');
        $this->assertGuest('student');
    }

    public function test_successful_login_clears_previous_failed_attempts(): void
    {
        $student = StudentFactory::new()->create();
        Livewire::test(LoginForm::class)
            ->set('login', $student->email)->set('password', 'incorrect')->call('submit')->assertHasErrors('login')
            ->set('password', 'password')->call('submit')->assertRedirect(route('student.dashboard'));

        $this->assertSame(0, RateLimiter::attempts('student-login:'.$student->email.'|127.0.0.1'));
    }

    public function test_authenticated_student_is_redirected_from_login(): void
    {
        $this->actingAs(StudentFactory::new()->create(), 'student');
        $this->get(route('student.login'))->assertRedirect(route('student.dashboard'));
        Livewire::test(LoginForm::class)->assertRedirect(route('student.dashboard'));
    }

    public function test_guests_cannot_access_student_dashboard_or_profile(): void
    {
        foreach (['student.dashboard', 'student.profile'] as $route) {
            $this->get(route($route))->assertRedirect(route('student.login'));
        }
    }

    public function test_inactive_authenticated_student_loses_access(): void
    {
        $this->actingAs(StudentFactory::new()->inactive()->create(), 'student')
            ->get(route('student.dashboard'))->assertRedirect(route('student.login'));
        $this->assertGuest('student');
    }

    public function test_dashboard_and_profile_show_only_the_authenticated_students_information(): void
    {
        $student = StudentFactory::new()->create(['name' => 'طالب الحساب الحالي']);
        $branch = BranchFactory::new()->create(['teacher_id' => $student->teacher_id, 'name' => 'فرع الطالب']);
        $student->update(['branch_id' => $branch->id]);
        StudentProfileFactory::new()->create(['student_id' => $student->id, 'school_name' => 'مدرسة الطالب']);
        $other = StudentFactory::new()->create(['name' => 'طالب من مركز آخر']);

        $this->actingAs($student, 'student')->get(route('student.dashboard'))
            ->assertOk()->assertSee($student->name)->assertSee('قريباً')->assertDontSee($other->name);
        $this->get(route('student.profile'))
            ->assertOk()->assertSee($student->code)->assertSee('الصف الأول الثانوي')
            ->assertSee('فرع الطالب')->assertSee('مدرسة الطالب')->assertDontSee($other->email)
            ->assertDontSee('assets/teacherPanel');
    }

    public function test_profile_handles_missing_optional_data_and_does_not_expose_other_tenants_branch(): void
    {
        $branch = BranchFactory::new()->create(['name' => 'فرع خاص بمركز آخر']);
        $student = StudentFactory::new()->create(['branch_id' => $branch->id, 'grade' => null, 'phone' => null]);
        $this->actingAs($student, 'student')->get(route('student.profile'))
            ->assertOk()->assertSee('غير محدد')->assertDontSee($branch->name);
    }

    public function test_student_can_update_account_fields_without_changing_tenant_or_school_data(): void
    {
        $student = StudentFactory::new()->create();
        $other = StudentFactory::new()->create();
        $profile = StudentProfileFactory::new()->create(['student_id' => $student->id]);
        $this->actingAs($student, 'student');

        Livewire::test(ProfileForm::class)
            ->call('startEditing', 'name')->assertSet('editing', 'name')
            ->set('name', 'اسم الطالب الجديد')->set('phone', '')
            ->call('save')->assertHasNoErrors()->assertSet('editing', null)
            ->assertDispatched('student-profile-updated');

        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'اسم الطالب الجديد', 'phone' => null, 'teacher_id' => $student->teacher_id, 'code' => $student->code]);
        $this->assertDatabaseHas('students', ['id' => $other->id, 'name' => $other->name]);
        $this->assertDatabaseHas('student_profiles', ['id' => $profile->id, 'school_name' => $profile->school_name]);
    }

    public function test_profile_rejects_duplicate_identity_fields_and_invalid_email(): void
    {
        $student = StudentFactory::new()->create();
        $other = StudentFactory::new()->create();
        $this->actingAs($student, 'student');

        Livewire::test(ProfileForm::class)
            ->set('username', $other->username)->set('email', $other->email)->set('phone', $other->phone)
            ->call('save')->assertHasErrors(['username', 'email', 'phone'])
            ->set('email', 'not-an-email')->call('save')->assertHasErrors('email');

        $this->assertDatabaseHas('students', ['id' => $student->id, 'username' => $student->username, 'email' => $student->email]);
    }

    public function test_profile_cancels_edits_and_rejects_internal_editing_fields(): void
    {
        $student = StudentFactory::new()->create();
        $this->actingAs($student, 'student');
        Livewire::test(ProfileForm::class)
            ->call('startEditing', 'teacher_id')->assertSet('editing', null)
            ->call('startEditing', 'name')->set('name', 'Unsaved')
            ->call('cancelEditing')->assertSet('name', $student->name)->assertSet('editing', null);
    }

    public function test_password_change_requires_current_password_and_confirmation(): void
    {
        $student = StudentFactory::new()->create();
        $this->actingAs($student, 'student');
        Livewire::test(ProfileForm::class)
            ->set('current_password', 'incorrect')->set('new_password', 'new-password')->set('new_password_confirmation', 'different')
            ->call('changePassword')->assertHasErrors(['current_password', 'new_password'])
            ->set('current_password', 'password')->set('new_password', 'short')->set('new_password_confirmation', 'short')
            ->call('changePassword')->assertHasErrors('new_password');

        $this->assertTrue(Hash::check('password', $student->fresh()->password));
    }

    public function test_student_can_change_password_and_log_in_with_it(): void
    {
        $student = StudentFactory::new()->create();
        $this->actingAs($student, 'student');
        Livewire::test(ProfileForm::class)
            ->set('current_password', 'password')->set('new_password', 'new-secure-password')->set('new_password_confirmation', 'new-secure-password')
            ->call('changePassword')->assertHasNoErrors()
            ->assertSet('current_password', '')->assertSet('new_password', '')->assertSet('new_password_confirmation', '')
            ->assertDispatched('student-password-updated');

        $this->assertTrue(Hash::check('new-secure-password', $student->fresh()->password));
        Auth::guard('student')->logout();
        Livewire::test(LoginForm::class)->set('login', $student->email)->set('password', 'new-secure-password')
            ->call('submit')->assertRedirect(route('student.dashboard'));
    }

    public function test_logout_invalidates_session_and_protected_pages_are_inaccessible(): void
    {
        $student = StudentFactory::new()->create();
        $this->actingAs($student, 'student');
        session()->put('student-session-test', 'value');
        $token = session()->token();
        Livewire::test(StudentProfileCard::class)->call('logout')->assertRedirect(route('student.login'));
        $this->assertGuest('student');
        $this->assertFalse(session()->has('student-session-test'));
        $this->assertNotSame($token, session()->token());
        $this->get(route('student.dashboard'))->assertRedirect(route('student.login'));
        $this->get(route('student.profile'))->assertRedirect(route('student.login'));
    }

    public function test_student_profile_actions_are_rejected_after_logout(): void
    {
        $student = StudentFactory::new()->create();
        $this->actingAs($student, 'student');
        $component = Livewire::test(ProfileForm::class)->set('name', 'Unauthorized name');
        Auth::guard('student')->logout();
        $component->call('save')->assertForbidden();
        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => $student->name]);
    }

    public function test_student_middleware_is_persistent_on_livewire_requests(): void
    {
        $this->assertContains(StudentMiddleware::class, app(PersistentMiddleware::class)->getPersistentMiddleware());

        $student = StudentFactory::new()->create();
        $html = $this->actingAs($student, 'student')->get(route('student.profile'))->assertOk()->getContent();
        preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);
        $snapshot = collect($matches[1])->first(fn ($value) => str_contains(html_entity_decode($value, ENT_QUOTES), 'student-panel.pages.profile.parts.profile-form'));
        $this->assertNotNull($snapshot);
        $student->update(['is_active' => 0]);

        $this->withHeader('X-Livewire', 'true')->postJson(route('default-livewire.update'), [
            'components' => [[
                'snapshot' => html_entity_decode($snapshot, ENT_QUOTES),
                'updates' => ['name' => 'Unauthorized update'],
                'calls' => [['path' => '', 'method' => 'save', 'params' => []]],
            ]],
        ])->assertRedirect(route('student.login'));

        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => $student->name]);
        $this->assertGuest('student');
    }

    public function test_teacher_and_admin_livewire_authentication_still_work_and_do_not_grant_student_access(): void
    {
        $teacher = TeacherFactory::new()->create();
        Livewire::test(\App\Livewire\TeacherPanel\Auth\LoginForm::class)
            ->set('login', $teacher->email)->set('password', 'password')->set('remember', false)
            ->call('submit')->assertHasNoErrors()->assertRedirect(route('teacher.dashboard'));
        $this->assertAuthenticatedAs($teacher, 'teacher');
        $this->get(route('student.dashboard'))->assertRedirect(route('student.login'));
        $this->get(route('teacher.login'))->assertOk();

        $admin = AdminFactory::new()->create();
        Livewire::test(\App\Livewire\AdminPanel\Auth\LoginForm::class)
            ->set('login', $admin->email)->set('password', 'password')->set('remember', false)
            ->call('submit')->assertHasNoErrors()->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertAuthenticatedAs($teacher, 'teacher');
        $this->assertGuest('student');
        $this->get(route('admin.login'))->assertOk();
    }

    public function test_routes_have_unique_names_and_student_assets_exist(): void
    {
        $names = collect(Route::getRoutes())->map(fn ($route) => $route->getName())->filter()->values();
        $this->assertSame($names->unique()->count(), $names->count());

        $views = glob(resource_path('views/student-panel/parts/*.blade.php'));
        foreach ($views as $view) {
            preg_match_all("/asset\\('([^']+)'\\)/", file_get_contents($view), $matches);
            foreach ($matches[1] as $asset) {
                $this->assertFileExists(public_path($asset));
            }
        }
        $this->assertFileEquals(public_path('assets/teacherPanel/images/logo.png'), public_path('assets/studentPanel/images/logo.png'));
        $this->assertFileExists(public_path('assets/studentPanel/fonts/phosphor/duotone/style.css'));

        $iconCss = file_get_contents(public_path('assets/studentPanel/fonts/tabler-icons.min.css'));
        foreach (File::allFiles(resource_path('views/student-panel')) as $view) {
            preg_match_all('/\\bti-[a-z0-9-]+\\b/', $view->getContents(), $matches);
            foreach (array_unique($matches[0]) as $icon) {
                $this->assertStringContainsString('.'.$icon.':before', $iconCss);
            }
        }
    }
}

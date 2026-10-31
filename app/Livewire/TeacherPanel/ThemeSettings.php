<?php

namespace App\Livewire\TeacherPanel;

use App\Models\TeacherThemeSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeSettings extends Component
{
    public bool $open = false;
    public string $themeMode = 'light';
    public string $themePreset = 'preset-1';
    public string $layoutWidth = 'full';

    public function mount(): void
    {
        $teacherId = Auth::guard('teacher')->id();

        $settings = TeacherThemeSetting::firstOrCreate(
            ['teacher_id' => $teacherId],
            [
                'theme_mode' => 'light',
                'accent_color' => 'preset-1',
                'layout_width' => 'full',
            ]
        );

        $this->themeMode = $settings->theme_mode;
        $this->themePreset = $settings->accent_color;
        $this->layoutWidth = $settings->layout_width;

        $this->setSettingsSession();
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function setThemeMode(string $mode): void
    {
        if (!in_array($mode, ['light', 'dark', 'system'], true)) {
            return;
        }

        $this->themeMode = $mode;
        $this->saveSettings();
        $this->applyTheme();
    }

    public function setAccentColor(string $preset): void
    {
        if (!preg_match('/^preset-(?:[1-9]|10)$/', $preset)) {
            return;
        }

        $this->themePreset = $preset;
        $this->saveSettings();
        $this->applyAccentColor();
    }

    public function resetSettings(): void
    {
        $this->themeMode = 'light';
        $this->themePreset = 'preset-1';
        $this->layoutWidth = 'full';

        $this->saveSettings();
        $this->dispatch('theme-settings-reset');
    }

    private function saveSettings(): void
    {
        TeacherThemeSetting::updateOrCreate(
            ['teacher_id' => Auth::guard('teacher')->id()],
            [
                'theme_mode' => $this->themeMode,
                'accent_color' => $this->themePreset,
                'layout_width' => $this->layoutWidth,
            ]
        );

        $this->setSettingsSession();
    }

    private function setSettingsSession(): void
    {
        session([
            'teacher_theme_settings' => [
                'theme_mode' => $this->themeMode,
                'accent_color' => $this->themePreset,
                'layout_width' => $this->layoutWidth,
            ],
        ]);
    }

    private function applyTheme(): void
    {
        $this->dispatch('apply-theme', mode: $this->themeMode);
    }

    private function applyAccentColor(): void
    {
        $this->dispatch('apply-accent-color', preset: $this->themePreset);
    }

    public function render()
    {
        return view('teacher-panel.theme-settings');
    }
}

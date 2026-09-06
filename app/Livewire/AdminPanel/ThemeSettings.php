<?php

namespace App\Livewire\AdminPanel;

use App\Models\AdminThemeSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeSettings extends Component
{
    public bool $open = false;
    public string $themeMode = 'light';
    public string $sidebarTheme = 'light';
    public string $themePreset = 'preset-1';
    public string $layoutWidth = 'full';

    public function mount(): void
    {
        $adminId = Auth::guard('admin')->id();

        $settings = AdminThemeSetting::firstOrCreate(
            ['admin_id' => $adminId],
            [
                'theme_mode' => 'light',
                'sidebar_theme' => 'light',
                'accent_color' => 'preset-1',
                'layout_width' => 'full',
            ]
        );

        $this->themeMode = $settings->theme_mode;
        $this->sidebarTheme = $settings->sidebar_theme;
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

    public function setSidebarTheme(string $theme): void
    {
        if (!in_array($theme, ['light', 'dark'], true)) {
            return;
        }

        $this->sidebarTheme = $theme;
        $this->saveSettings();
        $this->applySidebarTheme();
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
        $this->sidebarTheme = 'light';
        $this->themePreset = 'preset-1';
        $this->layoutWidth = 'full';

        $this->saveSettings();
        $this->dispatch('theme-settings-reset');
    }

    private function saveSettings(): void
    {
        AdminThemeSetting::updateOrCreate(
            ['admin_id' => Auth::guard('admin')->id()],
            [
                'theme_mode' => $this->themeMode,
                'sidebar_theme' => $this->sidebarTheme,
                'accent_color' => $this->themePreset,
                'layout_width' => $this->layoutWidth,
            ]
        );

        $this->setSettingsSession();
    }

    private function setSettingsSession(): void
    {
        session([
            'admin_theme_settings' => [
                'theme_mode' => $this->themeMode,
                'sidebar_theme' => $this->sidebarTheme,
                'accent_color' => $this->themePreset,
                'layout_width' => $this->layoutWidth,
            ],
        ]);
    }

    private function applyTheme(): void
    {
        $this->dispatch('apply-theme', mode: $this->themeMode);
    }

    private function applySidebarTheme(): void
    {
        $this->dispatch('apply-sidebar-theme', theme: $this->sidebarTheme);
    }

    private function applyAccentColor(): void
    {
        $this->dispatch('apply-accent-color', preset: $this->themePreset);
    }

    public function render()
    {
        return view('admin-panel.theme-settings');
    }
}

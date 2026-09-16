<?php

namespace App\Http\Controllers\TeacherPanel\Concerns;

use App\Enums\Admin\PackageFeatureKeyEnum;

trait ChecksSubscriptionLimit
{
    /**
     * فحص الحد وإرجاع redirect لو وصل
     */
    protected function checkLimitOrRedirect(int $featureKey, int $count, string $errorKey, string $route)
    {
        $teacher = $this->currentTeacher();
        $limit = $teacher->checkFeatureLimit($featureKey, $count);

        if (!$limit['can']) {
            return redirect()->route($route)->with($errorKey, $limit['message']);
        }

        return null;
    }
}

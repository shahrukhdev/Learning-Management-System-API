<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\AcademicClassInterface;
use App\Contracts\AttendanceInterface;
use App\Contracts\Auth\AuthInterface;
use App\Contracts\CourseInterface;
use App\Contracts\NoticeInterface;
use App\Contracts\PermissionInterface;
use App\Contracts\ProfileInterface;
use App\Contracts\RoleInterface;
use App\Contracts\StudentInterface;
use App\Contracts\TeacherInterface;
use App\Contracts\UserInterface;
use App\Contracts\Academic\ParentInterface;
use App\Contracts\Academic\StudentProgressInterface;
use App\Contracts\Dashboard\DashboardSummaryInterface;
use App\Contracts\Dashboard\StudentDashboardInterface;
use App\Contracts\Hifz\HifzSessionInterface;
use App\Contracts\Hifz\HifzStatisticsInterface;
use App\Contracts\Hifz\JuzInterface;
use App\Contracts\Hifz\QuranMapInterface;
use App\Contracts\Hifz\StudentSurahProgressInterface;
use App\Contracts\Hifz\SurahInterface;
use App\Contracts\System\ModuleInterface;
use App\Models\User;
use App\Observers\UserObserver;
use App\Repositories\AuthRepository;
use App\Repositories\Academic\AcademicClassRepository;
use App\Repositories\Academic\AttendanceRepository;
use App\Repositories\Academic\CourseRepository;
use App\Repositories\Academic\StudentRepository;
use App\Repositories\Academic\ParentRepository;
use App\Repositories\Academic\StudentProgressRepository;
use App\Repositories\Academic\TeacherRepository;
use App\Repositories\Dashboard\DashboardSummaryRepository;
use App\Repositories\Dashboard\StudentDashboardRepository;
use App\Repositories\Hifz\HifzSessionRepository;
use App\Repositories\Hifz\HifzStatisticsRepository;
use App\Repositories\Hifz\JuzRepository;
use App\Repositories\Hifz\QuranMapRepository;
use App\Repositories\Hifz\StudentSurahProgressRepository;
use App\Repositories\Hifz\SurahRepository;
use App\Repositories\Notice\NoticeRepository;
use App\Repositories\Profile\ProfileRepository;
use App\Repositories\System\ModuleRepository;
use App\Repositories\System\PermissionRepository;
use App\Repositories\System\RoleRepository;
use App\Repositories\System\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AcademicClassInterface::class, AcademicClassRepository::class);
        $this->app->bind(AttendanceInterface::class, AttendanceRepository::class);
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(CourseInterface::class, CourseRepository::class);
        $this->app->bind(DashboardSummaryInterface::class, DashboardSummaryRepository::class);
        $this->app->bind(StudentDashboardInterface::class, StudentDashboardRepository::class);
        $this->app->bind(HifzSessionInterface::class, HifzSessionRepository::class);
        $this->app->bind(HifzStatisticsInterface::class, HifzStatisticsRepository::class);
        $this->app->bind(JuzInterface::class, JuzRepository::class);
        $this->app->bind(ModuleInterface::class, ModuleRepository::class);
        $this->app->bind(NoticeInterface::class, NoticeRepository::class);
        $this->app->bind(ParentInterface::class, ParentRepository::class);
        $this->app->bind(StudentProgressInterface::class, StudentProgressRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(ProfileInterface::class, ProfileRepository::class);
        $this->app->bind(QuranMapInterface::class, QuranMapRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(StudentInterface::class, StudentRepository::class);
        $this->app->bind(StudentSurahProgressInterface::class, StudentSurahProgressRepository::class);
        $this->app->bind(SurahInterface::class, SurahRepository::class);
        $this->app->bind(TeacherInterface::class, TeacherRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}

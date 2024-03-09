<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Repositories\{
    Status\StatusInterface,
    Status\StatusRepository,
    User\UserInterface,
    User\UserRepository,
    UserType\UserTypeInterface,
    UserType\UserTypeRepository,
    Preference\PreferenceInterface,
    Preference\PreferenceRepository,
    Document\DocumentInterface,
    Document\DocumentRepository,
    Academy\AcademyInterface,
    Academy\AcademyRepository,
    Player\PlayerInterface,
    Player\PlayerRepository,
    Club\ClubInterface,
    Club\ClubRepository,
    ClubPlayer\ClubPlayerInterface,
    ClubPlayer\ClubPlayerRepository,
    ClubPresident\ClubPresidentInterface,
    ClubPresident\ClubPresidentRepository,
    Federation\FederationInterface,
    Federation\FederationRepository,
    Trainer\TrainerInterface,
    Trainer\TrainerRepository,
    Journalist\JournalistInterface,
    Journalist\JournalistRepository,
    Business\BusinessInterface,
    Business\BusinessRepository,
    Influencer\InfluencerInterface,
    Influencer\InfluencerRepository,
    Fan\FanInterface,
    Fan\FanRepository,
    Save\SaveInterface,
    Save\SaveRepository,
    Block\BlockInterface,
    Block\BlockRepository,
    Follow\FollowInterface,
    Follow\FollowRepository,
    SpamSection\SpamSectionInterface,
    SpamSection\SpamSectionRepository,
    Spam\SpamInterface,
    Spam\SpamRepository,
    Video\VideoInterface,
    Video\VideoRepository,
    Media\MediaInterface,
    Media\MediaRepository,
    AcademyPlayer\AcademyPlayerInterface,
    AcademyPlayer\AcademyPlayerRepository,
    Report\ReportInterface,
    Report\ReportRepository,
    Like\LikeInterface,
    Like\LikeRepository,
    Comment\CommentInterface,
    Comment\CommentRepository,
    View\ViewInterface,
    View\ViewRepository,
    Share\ShareInterface,
    Share\ShareRepository,
    Competition\CompetitionInterface,
    Competition\CompetitionRepository,
    AcademyPresident\AcademyPresidentInterface,
    AcademyPresident\AcademyPresidentRepository,
    Subscribe\SubscribeInterface,
    Subscribe\SubscribeRepository,
    Chat\ChatInterface,
    Chat\ChatRepository,
    Course\CourseInterface,
    Course\CourseRepository,
    ClubAchievment\ClubAchievmentInterface,
    ClubAchievment\ClubAchievmentRepository,
    Sound\SoundInterface,
    Sound\SoundRepository,
    Hashtag\HashtagInterface,
    Hashtag\HashtagRepository,
    Invoice\InvoiceInterface,
    Invoice\InvoiceRepository,
    FinancialSetting\FinancialSettingInterface,
    FinancialSetting\FinancialSettingRepository,
    FederationPresident\FederationPresidentInterface,
    FederationPresident\FederationPresidentRepository,
    Promote\PromoteInterface,
    Promote\PromoteRepository,
    Admin\AdminInterface,
    Admin\AdminRepository,
    Role\RoleInterface,
    Role\RoleRepository,
    ClubFeature\ClubFeatureInterface,
    ClubFeature\ClubFeatureReposatory,
    ContactMessage\ContactMessageInterface,
    ContactMessage\ContactMessageRepository,
    Permission\PermissionInterface,
    Permission\PermissionRepository,
    Sticker\StickerInterface,
    Sticker\StickerRepository,
    PromoteVideos\PromoteVideosInterface,
    PromoteVideos\PromoteVideosRepository,
    UserSubscribtion\UserSubscribtionInterface,
    UserSubscribtion\UserSubscribtionRepository,
    CourseSession\CourseSessionInterface,
    CourseSession\CourseSessionRepository,
    SessionLive\SessionLiveInterface,
    SessionLive\SessionLiveRepository,
    CompetitionSubscribtion\CompetitionSubscribtionInterface,
    CompetitionSubscribtion\CompetitionSubscribtionRepository,
    DeleteAccountRequest\DeleteAccountRequestInterface,
    DeleteAccountRequest\DeleteAccountRequestRepository,
    ChangeUserTypeRequest\ChangeUserTypeRequestInterface,
    ChangeUserTypeRequest\ChangeUserTypeRequestRepository,
};

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StatusInterface::class, StatusRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(UserTypeInterface::class, UserTypeRepository::class);
        $this->app->bind(DocumentInterface::class, DocumentRepository::class);
        $this->app->bind(AcademyInterface::class, AcademyRepository::class);
        $this->app->bind(PlayerInterface::class, PlayerRepository::class);
        $this->app->bind(ClubInterface::class, ClubRepository::class);
        $this->app->bind(ClubPlayerInterface::class, ClubPlayerRepository::class);
        $this->app->bind(ClubPresidentInterface::class, ClubPresidentRepository::class);
        $this->app->bind(FederationInterface::class, FederationRepository::class);
        $this->app->bind(TrainerInterface::class, TrainerRepository::class);
        $this->app->bind(JournalistInterface::class, JournalistRepository::class);
        $this->app->bind(BusinessInterface::class, BusinessRepository::class);
        $this->app->bind(InfluencerInterface::class, InfluencerRepository::class);
        $this->app->bind(FanInterface::class, FanRepository::class);
        $this->app->bind(PreferenceInterface::class, PreferenceRepository::class);
        $this->app->bind(SaveInterface::class, SaveRepository::class);
        $this->app->bind(BlockInterface::class, BlockRepository::class);
        $this->app->bind(FollowInterface::class, FollowRepository::class);
        $this->app->bind(SpamSectionInterface::class, SpamSectionRepository::class);
        $this->app->bind(SpamInterface::class, SpamRepository::class);
        $this->app->bind(VideoInterface::class, VideoRepository::class);
        $this->app->bind(MediaInterface::class, MediaRepository::class);
        $this->app->bind(AcademyPlayerInterface::class, AcademyPlayerRepository::class);
        $this->app->bind(ReportInterface::class, ReportRepository::class);
        $this->app->bind(LikeInterface::class, LikeRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(ViewInterface::class, ViewRepository::class);
        $this->app->bind(ShareInterface::class, ShareRepository::class);
        $this->app->bind(CompetitionInterface::class, CompetitionRepository::class);
        $this->app->bind(ClubAchievmentInterface::class, ClubAchievmentRepository::class);
        $this->app->bind(AcademyPresidentInterface::class, AcademyPresidentRepository::class);
        $this->app->bind(SubscribeInterface::class, SubscribeRepository::class);
        $this->app->bind(ChatInterface::class, ChatRepository::class);
        $this->app->bind(CourseInterface::class, CourseRepository::class);
        $this->app->bind(SoundInterface::class, SoundRepository::class);
        $this->app->bind(HashtagInterface::class, HashtagRepository::class);
        $this->app->bind(InvoiceInterface::class, InvoiceRepository::class);
        $this->app->bind(FinancialSettingInterface::class, FinancialSettingRepository::class);
        $this->app->bind(FederationPresidentInterface::class, FederationPresidentRepository::class);
        $this->app->bind(PromoteInterface::class, PromoteRepository::class);
        $this->app->bind(AdminInterface::class, AdminRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(ClubFeatureInterface::class, ClubFeatureReposatory::class);
        $this->app->bind(ContactMessageInterface::class, ContactMessageRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(StickerInterface::class, StickerRepository::class);
        $this->app->bind(PromoteVideosInterface::class, PromoteVideosRepository::class);
        $this->app->bind(UserSubscribtionInterface::class, UserSubscribtionRepository::class);
        $this->app->bind(CourseSessionInterface::class, CourseSessionRepository::class);
        $this->app->bind(SessionLiveInterface::class, SessionLiveRepository::class);
        $this->app->bind(CompetitionSubscribtionInterface::class, CompetitionSubscribtionRepository::class);
        $this->app->bind(DeleteAccountRequestInterface::class, DeleteAccountRequestRepository::class);
        $this->app->bind(ChangeUserTypeRequestInterface::class, ChangeUserTypeRequestRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    //
    }
}

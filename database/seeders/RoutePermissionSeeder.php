<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoutePermission;

class RoutePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            /**
             * admin
             */
            // admin.view
            [
                'permission_slug' => 'admin.view',
                'route_slug' => 'admin.view',
            ],
            [
                'permission_slug' => 'admin.view',
                'route_slug' => 'admin.id',
            ],

            // admin.create
            [
                'permission_slug' => 'admin.create',
                'route_slug' => 'admin.view',
            ],
            [
                'permission_slug' => 'admin.create',
                'route_slug' => 'admin.id',
            ],
            [
                'permission_slug' => 'admin.create',
                'route_slug' => 'admin.create',
            ],
            [
                'permission_slug' => 'admin.create',
                'route_slug' => 'role.view',
            ],
            [
                'permission_slug' => 'admin.create',
                'route_slug' => 'role.id',
            ],

            // admin.edit
            [
                'permission_slug' => 'admin.edit',
                'route_slug' => 'admin.view',
            ],
            [
                'permission_slug' => 'admin.edit',
                'route_slug' => 'admin.id',
            ],
            [
                'permission_slug' => 'admin.edit',
                'route_slug' => 'admin.edit',
            ],
            [
                'permission_slug' => 'admin.edit',
                'route_slug' => 'role.view',
            ],
            [
                'permission_slug' => 'admin.edit',
                'route_slug' => 'role.id',
            ],

            // admin.delete
            [
                'permission_slug' => 'admin.delete',
                'route_slug' => 'admin.view',
            ],
            [
                'permission_slug' => 'admin.delete',
                'route_slug' => 'admin.id',
            ],
            [
                'permission_slug' => 'admin.delete',
                'route_slug' => 'admin.delete',
            ],

            /**
             * role
             */
            // role.view
            [
                'permission_slug' => 'role.view',
                'route_slug' => 'role.view',
            ],
            [
                'permission_slug' => 'role.view',
                'route_slug' => 'role.id',
            ],

            // role.create
            [
                'permission_slug' => 'role.create',
                'route_slug' => 'role.view',
            ],
            [
                'permission_slug' => 'role.create',
                'route_slug' => 'role.id',
            ],
            [
                'permission_slug' => 'role.create',
                'route_slug' => 'role.create',
            ],
            [
                'permission_slug' => 'role.create',
                'route_slug' => 'permission.view',
            ],
            [
                'permission_slug' => 'role.create',
                'route_slug' => 'permission.id',
            ],

            // role.edit
            [
                'permission_slug' => 'role.edit',
                'route_slug' => 'role.view',
            ],
            [
                'permission_slug' => 'role.edit',
                'route_slug' => 'role.id',
            ],
            [
                'permission_slug' => 'role.edit',
                'route_slug' => 'role.edit',
            ],
            [
                'permission_slug' => 'role.edit',
                'route_slug' => 'permission.view',
            ],
            [
                'permission_slug' => 'role.edit',
                'route_slug' => 'permission.id',
            ],

            // role.delete
            [
                'permission_slug' => 'role.delete',
                'route_slug' => 'role.view',
            ],
            [
                'permission_slug' => 'role.delete',
                'route_slug' => 'role.id',
            ],
            [
                'permission_slug' => 'role.delete',
                'route_slug' => 'role.delete',
            ],

            /**
             * currency
             */
            // currency.view
            /* [
                'permission_slug' => 'currency.view',
                'route_slug' => 'currency.view',
            ],
            [
                'permission_slug' => 'currency.view',
                'route_slug' => 'currency.id',
            ], */

            /**
             * country
             */
            // country.view
            [
                'permission_slug' => 'country.view',
                'route_slug' => 'country.view',
            ],
            [
                'permission_slug' => 'country.view',
                'route_slug' => 'country.id',
            ],

            // country.create
            [
                'permission_slug' => 'country.create',
                'route_slug' => 'country.view',
            ],
            [
                'permission_slug' => 'country.create',
                'route_slug' => 'country.id',
            ],
            [
                'permission_slug' => 'country.create',
                'route_slug' => 'country.create',
            ],

            // country.edit
            [
                'permission_slug' => 'country.edit',
                'route_slug' => 'country.view',
            ],
            [
                'permission_slug' => 'country.edit',
                'route_slug' => 'country.id',
            ],
            [
                'permission_slug' => 'country.edit',
                'route_slug' => 'country.edit',
            ],

            // country.delete
            [
                'permission_slug' => 'country.delete',
                'route_slug' => 'country.view',
            ],
            [
                'permission_slug' => 'country.delete',
                'route_slug' => 'country.id',
            ],
            [
                'permission_slug' => 'country.delete',
                'route_slug' => 'country.delete',
            ],

            /**
             * city
             */
            // city.view
            [
                'permission_slug' => 'city.view',
                'route_slug' => 'city.view',
            ],
            [
                'permission_slug' => 'city.view',
                'route_slug' => 'city.id',
            ],

            // city.create
            [
                'permission_slug' => 'city.create',
                'route_slug' => 'city.view',
            ],
            [
                'permission_slug' => 'city.create',
                'route_slug' => 'city.id',
            ],
            [
                'permission_slug' => 'city.create',
                'route_slug' => 'city.create',
            ],
            [
                'permission_slug' => 'city.create',
                'route_slug' => 'country.view',
            ],
            [
                'permission_slug' => 'city.create',
                'route_slug' => 'country.id',
            ],

            // city.edit
            [
                'permission_slug' => 'city.edit',
                'route_slug' => 'city.view',
            ],
            [
                'permission_slug' => 'city.edit',
                'route_slug' => 'city.id',
            ],
            [
                'permission_slug' => 'city.edit',
                'route_slug' => 'city.edit',
            ],
            [
                'permission_slug' => 'city.edit',
                'route_slug' => 'country.view',
            ],
            [
                'permission_slug' => 'city.edit',
                'route_slug' => 'country.id',
            ],

            // city.delete
            [
                'permission_slug' => 'city.delete',
                'route_slug' => 'city.view',
            ],
            [
                'permission_slug' => 'city.delete',
                'route_slug' => 'city.id',
            ],
            [
                'permission_slug' => 'city.delete',
                'route_slug' => 'city.delete',
            ],

            /**
             * spam
             */
            // spam.view
            [
                'permission_slug' => 'spam.view',
                'route_slug' => 'spam.view',
            ],
            [
                'permission_slug' => 'spam.view',
                'route_slug' => 'spam.id',
            ],

            // spam.create
            [
                'permission_slug' => 'spam.create',
                'route_slug' => 'spam.view',
            ],
            [
                'permission_slug' => 'spam.create',
                'route_slug' => 'spam.id',
            ],
            [
                'permission_slug' => 'spam.create',
                'route_slug' => 'spam.create',
            ],
            [
                'permission_slug' => 'spam.create',
                'route_slug' => 'spam-section.view',
            ],
            [
                'permission_slug' => 'spam.create',
                'route_slug' => 'spam-section.id',
            ],

            // spam.edit
            [
                'permission_slug' => 'spam.edit',
                'route_slug' => 'spam.view',
            ],
            [
                'permission_slug' => 'spam.edit',
                'route_slug' => 'spam.id',
            ],
            [
                'permission_slug' => 'spam.edit',
                'route_slug' => 'spam.edit',
            ],
            [
                'permission_slug' => 'spam.edit',
                'route_slug' => 'spam-section.view',
            ],
            [
                'permission_slug' => 'spam.edit',
                'route_slug' => 'spam-section.id',
            ],

            // spam.delete
            [
                'permission_slug' => 'spam.delete',
                'route_slug' => 'spam.view',
            ],
            [
                'permission_slug' => 'spam.delete',
                'route_slug' => 'spam.id',
            ],
            [
                'permission_slug' => 'spam.delete',
                'route_slug' => 'spam.delete',
            ],

            /**
             * setting
             */
            // setting.view
            [
                'permission_slug' => 'setting.view',
                'route_slug' => 'setting.id',
            ],
            [
                'permission_slug' => 'setting.view',
                'route_slug' => 'financial-setting.id',
            ],

            // setting.edit
            [
                'permission_slug' => 'setting.edit',
                'route_slug' => 'setting.id',
            ],
            [
                'permission_slug' => 'setting.edit',
                'route_slug' => 'setting.edit',
            ],
            [
                'permission_slug' => 'setting.edit',
                'route_slug' => 'financial-setting.id',
            ],
            [
                'permission_slug' => 'setting.edit',
                'route_slug' => 'financial-setting.edit',
            ],

            /**
             * academy-level
             */
            // academy-level.view
            [
                'permission_slug' => 'academy-level.view',
                'route_slug' => 'academy-level.view',
            ],
            [
                'permission_slug' => 'academy-level.view',
                'route_slug' => 'academy-level.id',
            ],

            // academy-level.create
            [
                'permission_slug' => 'academy-level.create',
                'route_slug' => 'academy-level.view',
            ],
            [
                'permission_slug' => 'academy-level.create',
                'route_slug' => 'academy-level.id',
            ],
            [
                'permission_slug' => 'academy-level.create',
                'route_slug' => 'academy-level.create',
            ],

            // academy-level.edit
            [
                'permission_slug' => 'academy-level.edit',
                'route_slug' => 'academy-level.view',
            ],
            [
                'permission_slug' => 'academy-level.edit',
                'route_slug' => 'academy-level.id',
            ],
            [
                'permission_slug' => 'academy-level.edit',
                'route_slug' => 'academy-level.edit',
            ],

            // academy-level.delete
            [
                'permission_slug' => 'academy-level.delete',
                'route_slug' => 'academy-level.view',
            ],
            [
                'permission_slug' => 'academy-level.delete',
                'route_slug' => 'academy-level.id',
            ],
            [
                'permission_slug' => 'academy-level.delete',
                'route_slug' => 'academy-level.delete',
            ],

            /**
             * player-footnesse
             */
            // player-footnesse.view
            [
                'permission_slug' => 'player-footnesse.view',
                'route_slug' => 'player-footnesse.view',
            ],
            [
                'permission_slug' => 'player-footnesse.view',
                'route_slug' => 'player-footnesse.id',
            ],

            // player-footnesse.create
            [
                'permission_slug' => 'player-footnesse.create',
                'route_slug' => 'player-footnesse.view',
            ],
            [
                'permission_slug' => 'player-footnesse.create',
                'route_slug' => 'player-footnesse.id',
            ],
            [
                'permission_slug' => 'player-footnesse.create',
                'route_slug' => 'player-footnesse.create',
            ],

            // player-footnesse.edit
            [
                'permission_slug' => 'player-footnesse.edit',
                'route_slug' => 'player-footnesse.view',
            ],
            [
                'permission_slug' => 'player-footnesse.edit',
                'route_slug' => 'player-footnesse.id',
            ],
            [
                'permission_slug' => 'player-footnesse.edit',
                'route_slug' => 'player-footnesse.edit',
            ],

            // player-footnesse.delete
            [
                'permission_slug' => 'player-footnesse.delete',
                'route_slug' => 'player-footnesse.view',
            ],
            [
                'permission_slug' => 'player-footnesse.delete',
                'route_slug' => 'player-footnesse.id',
            ],
            [
                'permission_slug' => 'player-footnesse.delete',
                'route_slug' => 'player-footnesse.delete',
            ],

            /**
             * player-position
             */
            // player-position.view
            [
                'permission_slug' => 'player-position.view',
                'route_slug' => 'player-position.view',
            ],
            [
                'permission_slug' => 'player-position.view',
                'route_slug' => 'player-position.id',
            ],

            // player-position.create
            [
                'permission_slug' => 'player-position.create',
                'route_slug' => 'player-position.view',
            ],
            [
                'permission_slug' => 'player-position.create',
                'route_slug' => 'player-position.id',
            ],
            [
                'permission_slug' => 'player-position.create',
                'route_slug' => 'player-position.create',
            ],

            // player-position.edit
            [
                'permission_slug' => 'player-position.edit',
                'route_slug' => 'player-position.view',
            ],
            [
                'permission_slug' => 'player-position.edit',
                'route_slug' => 'player-position.id',
            ],
            [
                'permission_slug' => 'player-position.edit',
                'route_slug' => 'player-position.edit',
            ],

            // player-position.delete
            [
                'permission_slug' => 'player-position.delete',
                'route_slug' => 'player-position.view',
            ],
            [
                'permission_slug' => 'player-position.delete',
                'route_slug' => 'player-position.id',
            ],
            [
                'permission_slug' => 'player-position.delete',
                'route_slug' => 'player-position.delete',
            ],

            /**
             * trainer-experience-level
             */
            // trainer-experience-level.view
            [
                'permission_slug' => 'trainer-experience-level.view',
                'route_slug' => 'trainer-experience-level.view',
            ],
            [
                'permission_slug' => 'trainer-experience-level.view',
                'route_slug' => 'trainer-experience-level.id',
            ],

            // trainer-experience-level.create
            [
                'permission_slug' => 'trainer-experience-level.create',
                'route_slug' => 'trainer-experience-level.view',
            ],
            [
                'permission_slug' => 'trainer-experience-level.create',
                'route_slug' => 'trainer-experience-level.id',
            ],
            [
                'permission_slug' => 'trainer-experience-level.create',
                'route_slug' => 'trainer-experience-level.create',
            ],

            // trainer-experience-level.edit
            [
                'permission_slug' => 'trainer-experience-level.edit',
                'route_slug' => 'trainer-experience-level.view',
            ],
            [
                'permission_slug' => 'trainer-experience-level.edit',
                'route_slug' => 'trainer-experience-level.id',
            ],
            [
                'permission_slug' => 'trainer-experience-level.edit',
                'route_slug' => 'trainer-experience-level.edit',
            ],

            // trainer-experience-level.delete
            [
                'permission_slug' => 'trainer-experience-level.delete',
                'route_slug' => 'trainer-experience-level.view',
            ],
            [
                'permission_slug' => 'trainer-experience-level.delete',
                'route_slug' => 'trainer-experience-level.id',
            ],
            [
                'permission_slug' => 'trainer-experience-level.delete',
                'route_slug' => 'trainer-experience-level.delete',
            ],

            /**
             * blog
             */
            // blog.view
            [
                'permission_slug' => 'blog.view',
                'route_slug' => 'blog.view',
            ],
            [
                'permission_slug' => 'blog.view',
                'route_slug' => 'blog.id',
            ],

            // blog.create
            [
                'permission_slug' => 'blog.create',
                'route_slug' => 'blog.view',
            ],
            [
                'permission_slug' => 'blog.create',
                'route_slug' => 'blog.id',
            ],
            [
                'permission_slug' => 'blog.create',
                'route_slug' => 'blog.create',
            ],

            // blog.edit
            [
                'permission_slug' => 'blog.edit',
                'route_slug' => 'blog.view',
            ],
            [
                'permission_slug' => 'blog.edit',
                'route_slug' => 'blog.id',
            ],
            [
                'permission_slug' => 'blog.edit',
                'route_slug' => 'blog.edit',
            ],

            // blog.delete
            [
                'permission_slug' => 'blog.delete',
                'route_slug' => 'blog.view',
            ],
            [
                'permission_slug' => 'blog.delete',
                'route_slug' => 'blog.id',
            ],
            [
                'permission_slug' => 'blog.delete',
                'route_slug' => 'blog.delete',
            ],

            /**
             * tag
             */
            // tag.view
            [
                'permission_slug' => 'tag.view',
                'route_slug' => 'tag.view',
            ],
            [
                'permission_slug' => 'tag.view',
                'route_slug' => 'tag.id',
            ],

            // tag.create
            [
                'permission_slug' => 'tag.create',
                'route_slug' => 'tag.view',
            ],
            [
                'permission_slug' => 'tag.create',
                'route_slug' => 'tag.id',
            ],
            [
                'permission_slug' => 'tag.create',
                'route_slug' => 'tag.create',
            ],

            // tag.edit
            [
                'permission_slug' => 'tag.edit',
                'route_slug' => 'tag.view',
            ],
            [
                'permission_slug' => 'tag.edit',
                'route_slug' => 'tag.id',
            ],
            [
                'permission_slug' => 'tag.edit',
                'route_slug' => 'tag.edit',
            ],

            // tag.delete
            [
                'permission_slug' => 'tag.delete',
                'route_slug' => 'tag.view',
            ],
            [
                'permission_slug' => 'tag.delete',
                'route_slug' => 'tag.id',
            ],
            [
                'permission_slug' => 'tag.delete',
                'route_slug' => 'tag.delete',
            ],

            /**
             * user
             */
            // user.view
            [
                'permission_slug' => 'user.view',
                'route_slug' => 'user.view',
            ],
            [
                'permission_slug' => 'user.view',
                'route_slug' => 'user.id',
            ],

            // user.accept-reject
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'user.view',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'academy.accept',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'academy.reject',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'player.accept',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'player.reject',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'trainer.accept',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'trainer.reject',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'journalist.accept',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'journalist.reject',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'business.accept',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'business.reject',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'influencer.accept',
            ],
            [
                'permission_slug' => 'user.accept-reject',
                'route_slug' => 'influencer.reject',
            ],

            // user.block-unblock
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'user.view',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'academy.block',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'academy.unblock',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'player.block',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'player.unblock',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'trainer.block',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'trainer.unblock',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'journalist.block',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'journalist.unblock',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'business.block',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'business.unblock',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'influencer.block',
            ],
            [
                'permission_slug' => 'user.block-unblock',
                'route_slug' => 'influencer.unblock',
            ],

            /**
             * academy
             */
            // academy.view
            [
                'permission_slug' => 'academy.view',
                'route_slug' => 'academy.view',
            ],
            [
                'permission_slug' => 'academy.view',
                'route_slug' => 'academy.id',
            ],
            [
                'permission_slug' => 'academy.view',
                'route_slug' => 'academy.overview',
            ],
            [
                'permission_slug' => 'academy.view',
                'route_slug' => 'academy-player.view',
            ],

            /**
             * player
             */
            // player.view
            [
                'permission_slug' => 'player.view',
                'route_slug' => 'player.view',
            ],
            [
                'permission_slug' => 'player.view',
                'route_slug' => 'player.id',
            ],
            [
                'permission_slug' => 'player.view',
                'route_slug' => 'player.overview',
            ],

            /**
             * trainer
             */
            // trainer.view
            [
                'permission_slug' => 'trainer.view',
                'route_slug' => 'trainer.view',
            ],
            [
                'permission_slug' => 'trainer.view',
                'route_slug' => 'trainer.id',
            ],
            [
                'permission_slug' => 'trainer.view',
                'route_slug' => 'trainer.overview',
            ],
            [
                'permission_slug' => 'trainer.view',
                'route_slug' => 'course.view',
            ],
            [
                'permission_slug' => 'trainer.view',
                'route_slug' => 'course.id',
            ],

            /**
             * journalist
             */
            // journalist.view
            [
                'permission_slug' => 'journalist.view',
                'route_slug' => 'journalist.view',
            ],
            [
                'permission_slug' => 'journalist.view',
                'route_slug' => 'journalist.id',
            ],
            [
                'permission_slug' => 'journalist.view',
                'route_slug' => 'journalist.overview',
            ],

            /**
             * business
             */
            // business.view
            [
                'permission_slug' => 'business.view',
                'route_slug' => 'business.view',
            ],
            [
                'permission_slug' => 'business.view',
                'route_slug' => 'business.id',
            ],
            [
                'permission_slug' => 'business.view',
                'route_slug' => 'business.overview',
            ],

            /**
             * influencer
             */
            // influencer.view
            [
                'permission_slug' => 'influencer.view',
                'route_slug' => 'influencer.view',
            ],
            [
                'permission_slug' => 'influencer.view',
                'route_slug' => 'influencer.id',
            ],
            [
                'permission_slug' => 'influencer.view',
                'route_slug' => 'influencer.overview',
            ],

            /**
             * fan
             */
            // fan.view
            [
                'permission_slug' => 'fan.view',
                'route_slug' => 'fan.view',
            ],
            [
                'permission_slug' => 'fan.view',
                'route_slug' => 'fan.id',
            ],
            [
                'permission_slug' => 'fan.view',
                'route_slug' => 'fan.overview',
            ],

            /**
             * club
             */
            // club.view
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'club.view',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'club.id',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'club.overview',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'club.club-president.view',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'competition.view',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'club.club-feature.view',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'club-player.view',
            ],
            [
                'permission_slug' => 'club.view',
                'route_slug' => 'plan.view',
            ],

            // club.create
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.view',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.id',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.overview',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.create',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.club-president.view',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.club-president.create',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.club-competition.view',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club.club-feature.view',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club-player.view',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'club-player.create',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'plan.view',
            ],
            [
                'permission_slug' => 'club.create',
                'route_slug' => 'plan.create',
            ],

            // club.edit
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.view',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.id',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.overview',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.edit',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.club-president.view',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.club-president.create',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.club-president.edit',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.club-competition.view',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.club-feature.view',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club.club-feature.edit',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club-player.view',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club-player.edit',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'club-player.delete',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'plan.view',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'plan.create',
            ],
            [
                'permission_slug' => 'club.edit',
                'route_slug' => 'plan.edit',
            ],

            /**
             * federation
             */
            // federation.view
            [
                'permission_slug' => 'federation.view',
                'route_slug' => 'federation.view',
            ],
            [
                'permission_slug' => 'federation.view',
                'route_slug' => 'federation.id',
            ],
            [
                'permission_slug' => 'federation.view',
                'route_slug' => 'federation.overview',
            ],
            [
                'permission_slug' => 'federation.view',
                'route_slug' => 'federation.federation-president.view',
            ],

            // federation.create
            [
                'permission_slug' => 'federation.create',
                'route_slug' => 'federation.view',
            ],
            [
                'permission_slug' => 'federation.create',
                'route_slug' => 'federation.id',
            ],
            [
                'permission_slug' => 'federation.create',
                'route_slug' => 'federation.overview',
            ],
            [
                'permission_slug' => 'federation.create',
                'route_slug' => 'federation.create',
            ],
            [
                'permission_slug' => 'federation.create',
                'route_slug' => 'federation.federation-president.view',
            ],
            [
                'permission_slug' => 'federation.create',
                'route_slug' => 'federation.federation-president.create',
            ],

            // federation.edit
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.view',
            ],
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.id',
            ],
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.overview',
            ],
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.edit',
            ],
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.federation-president.view',
            ],
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.federation-president.create',
            ],
            [
                'permission_slug' => 'federation.edit',
                'route_slug' => 'federation.federation-president.edit',
            ],

            /**
             * report
             */
            // report.view
            [
                'permission_slug' => 'report.view',
                'route_slug' => 'report.view',
            ],
            [
                'permission_slug' => 'report.view',
                'route_slug' => 'report.id',
            ],
            [
                'permission_slug' => 'report.view',
                'route_slug' => 'report.read',
            ],
            [
                'permission_slug' => 'report.view',
                'route_slug' => 'report.unread',
            ],
            [
                'permission_slug' => 'report.view',
                'route_slug' => 'video.id',
            ],
            [
                'permission_slug' => 'report.view',
                'route_slug' => 'comment.id',
            ],

            // report.delete
            [
                'permission_slug' => 'report.delete',
                'route_slug' => 'report.view',
            ],
            [
                'permission_slug' => 'report.delete',
                'route_slug' => 'report.id',
            ],
            [
                'permission_slug' => 'report.delete',
                'route_slug' => 'report.delete',
            ],
            [
                'permission_slug' => 'report.delete',
                'route_slug' => 'report.delete',
            ],
            [
                'permission_slug' => 'report.delete',
                'route_slug' => 'video.id',
            ],
            [
                'permission_slug' => 'report.delete',
                'route_slug' => 'comment.id',
            ],

            // report.action
            [
                'permission_slug' => 'report.action',
                'route_slug' => 'report.view',
            ],
            [
                'permission_slug' => 'report.action',
                'route_slug' => 'report.id',
            ],
            [
                'permission_slug' => 'report.action',
                'route_slug' => 'video.id',
            ],
            [
                'permission_slug' => 'report.action',
                'route_slug' => 'video.delete',
            ],
            [
                'permission_slug' => 'report.action',
                'route_slug' => 'comment.id',
            ],
            [
                'permission_slug' => 'report.action',
                'route_slug' => 'comment.delete',
            ],

            /**
             * sound
             */
            // sound.view
            [
                'permission_slug' => 'sound.view',
                'route_slug' => 'sound.view',
            ],
            [
                'permission_slug' => 'sound.view',
                'route_slug' => 'sound.id',
            ],

            // sound.create
            [
                'permission_slug' => 'sound.create',
                'route_slug' => 'sound.view',
            ],
            [
                'permission_slug' => 'sound.create',
                'route_slug' => 'sound.id',
            ],
            [
                'permission_slug' => 'sound.create',
                'route_slug' => 'sound.create',
            ],

            // sound.edit
            [
                'permission_slug' => 'sound.edit',
                'route_slug' => 'sound.view',
            ],
            [
                'permission_slug' => 'sound.edit',
                'route_slug' => 'sound.id',
            ],
            [
                'permission_slug' => 'sound.edit',
                'route_slug' => 'sound.edit',
            ],

            // sound.delete
            [
                'permission_slug' => 'sound.delete',
                'route_slug' => 'sound.view',
            ],
            [
                'permission_slug' => 'sound.delete',
                'route_slug' => 'sound.id',
            ],
            [
                'permission_slug' => 'sound.delete',
                'route_slug' => 'sound.delete',
            ],

            /**
             * contact
             */
            // contact.view
            [
                'permission_slug' => 'contact.view',
                'route_slug' => 'contact.view',
            ],
            [
                'permission_slug' => 'contact.view',
                'route_slug' => 'contact.id',
            ],
            [
                'permission_slug' => 'contact.view',
                'route_slug' => 'contact.read',
            ],
            [
                'permission_slug' => 'contact.view',
                'route_slug' => 'contact.unread',
            ],

            // contact.delete
            [
                'permission_slug' => 'contact.delete',
                'route_slug' => 'contact.view',
            ],
            [
                'permission_slug' => 'contact.delete',
                'route_slug' => 'contact.id',
            ],
            [
                'permission_slug' => 'contact.delete',
                'route_slug' => 'contact.delete',
            ],

            /**
             * sticker
             */
            // sticker.view
            [
                'permission_slug' => 'sticker.view',
                'route_slug' => 'sticker.view',
            ],
            [
                'permission_slug' => 'sticker.view',
                'route_slug' => 'sticker.id',
            ],

            // sticker.create
            [
                'permission_slug' => 'sticker.create',
                'route_slug' => 'sticker.view',
            ],
            [
                'permission_slug' => 'sticker.create',
                'route_slug' => 'sticker.id',
            ],
            [
                'permission_slug' => 'sticker.create',
                'route_slug' => 'sticker.create',
            ],

            // sticker.delete
            [
                'permission_slug' => 'sticker.delete',
                'route_slug' => 'sticker.view',
            ],
            [
                'permission_slug' => 'sticker.delete',
                'route_slug' => 'sticker.id',
            ],
            [
                'permission_slug' => 'sticker.delete',
                'route_slug' => 'sticker.delete',
            ],

            /**
             * promote
             */
            // promote.view
            [
                'permission_slug' => 'promote.view',
                'route_slug' => 'promote.view',
            ],
            [
                'permission_slug' => 'promote.view',
                'route_slug' => 'promote.id',
            ],
        ];

        foreach ($data as $item) {
            $route_permission = RoutePermission::create($item);
        }

    }
}

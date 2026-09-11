<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Article;
use App\Models\CoachProfile;
use App\Models\Inquiry;
use App\Models\Job;
use App\Models\MatchingMaster;
use App\Models\Offer;
use App\Models\Organization;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        CoachProfile::whereNull('user_id')->delete();
        Organization::whereNull('user_id')->delete();

        $admin = User::updateOrCreate(
            ['email' => 'admin@highclass-inc.com'],
            [
                'name' => 'ハイクラス管理者',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'member_type' => null,
                'status' => 'approved',
            ]
        );

        $coachUsers = [
            'itaka@highclass-inc.com' => '位髙 駿夫',
            'mori.coach@example.com' => '森 直樹',
            'sato.trainer@example.com' => '佐藤 美咲',
            'tanaka.mental@example.com' => '田中 達也',
            'yamada.nutrition@example.com' => '山田 彩',
            'kobayashi.analysis@example.com' => '小林 健',
        ];

        foreach ($coachUsers as $email => $name) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'coach',
                    'member_type' => 'coach_member',
                    'status' => 'approved',
                ]
            );
        }

        $organizationUsers = [
            'aoyama-hs@example.com' => '青山高等学校',
            'tokai-club@example.com' => '東海ジュニアスポーツクラブ',
            'kansai-academy@example.com' => '関西アスリートアカデミー',
            'fukuoka-city@example.com' => '福岡市地域スポーツ推進室',
        ];

        foreach ($organizationUsers as $email => $name) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'organization',
                    'member_type' => 'browsing_member',
                    'status' => 'approved',
                ]
            );
        }

        $coaches = [
            [
                'email' => 'itaka@highclass-inc.com',
                'name' => '位髙 駿夫',
                'kana' => 'いたか としお',
                'roman_name' => 'Toshio Itaka',
                'birth_year' => 1988,
                'affiliation' => '株式会社ハイクラス / スポーツ健康科学研究室',
                'main_prefecture' => '東京',
                'area' => '渋谷区・都内全域',
                'sports' => ['柔道', 'バドミントン'],
                'fields' => ['競技指導', 'トレーニング'],
                'degree' => '博士（スポーツ健康科学）',
                'qualifications' => '健康運動指導士、スポーツプログラマー',
                'keywords' => '部活動改革、測定評価、体力向上、怪我予防',
                'achievements' => '大学運動部、地域クラブ、企業チームでのトレーニング設計と現場支援を多数担当。',
                'desired_fee_range' => '30,000円〜 / 60分',
                'message' => '科学的な評価と現場で続けられるメニュー設計を大切にしています。',
            ],
            [
                'email' => 'mori.coach@example.com',
                'name' => '森 直樹',
                'kana' => 'もり なおき',
                'roman_name' => 'Naoki Mori',
                'birth_year' => 1982,
                'affiliation' => '横浜バスケットボールラボ',
                'main_prefecture' => '神奈川',
                'area' => '横浜・川崎',
                'sports' => ['バスケットボール'],
                'fields' => ['競技指導'],
                'degree' => '体育学士',
                'qualifications' => 'JBA公認コーチ',
                'keywords' => 'チーム戦術、育成年代、ファンダメンタル',
                'achievements' => '中学・高校チームの県大会上位進出を複数サポート。',
                'desired_fee_range' => '20,000円〜 / 90分',
                'message' => '基礎技術と判断力を同時に伸ばす練習づくりを行います。',
            ],
            [
                'email' => 'sato.trainer@example.com',
                'name' => '佐藤 美咲',
                'kana' => 'さとう みさき',
                'roman_name' => 'Misaki Sato',
                'birth_year' => 1991,
                'affiliation' => '名古屋コンディショニングセンター',
                'main_prefecture' => '愛知',
                'area' => '名古屋周辺',
                'sports' => ['ハンドボール', '陸上競技'],
                'fields' => ['トレーニング', 'リハビリ'],
                'degree' => '理学療法学士',
                'qualifications' => '理学療法士、NSCA-CPT',
                'keywords' => 'フィジカル強化、コンディショニング、傷害予防',
                'achievements' => 'ジュニアから社会人チームまで、年間コンディショニング計画を支援。',
                'desired_fee_range' => '25,000円〜 / 60分',
                'message' => '怪我を減らしながら競技力を上げる現実的なプログラムを提案します。',
            ],
            [
                'email' => 'tanaka.mental@example.com',
                'name' => '田中 達也',
                'kana' => 'たなか たつや',
                'roman_name' => 'Tatsuya Tanaka',
                'birth_year' => 1986,
                'affiliation' => '関西スポーツメンタルオフィス',
                'main_prefecture' => '大阪',
                'area' => '大阪・兵庫・京都',
                'sports' => ['サッカー', 'テニス'],
                'fields' => ['メンタル'],
                'degree' => '心理学修士',
                'qualifications' => 'スポーツメンタルトレーニング指導士',
                'keywords' => '目標設定、試合前準備、チームビルディング',
                'achievements' => '全国大会出場校、地域クラブのメンタル研修を担当。',
                'desired_fee_range' => '応相談',
                'message' => '選手が自分で整えられるメンタルスキルを育てます。',
            ],
            [
                'email' => 'yamada.nutrition@example.com',
                'name' => '山田 彩',
                'kana' => 'やまだ あや',
                'roman_name' => 'Aya Yamada',
                'birth_year' => 1993,
                'affiliation' => '福岡スポーツ栄養サポート',
                'main_prefecture' => '福岡',
                'area' => '福岡市・久留米',
                'sports' => ['野球', 'バレーボール'],
                'fields' => ['栄養'],
                'degree' => '栄養学士',
                'qualifications' => '管理栄養士、公認スポーツ栄養士',
                'keywords' => '補食、成長期、遠征食、体づくり',
                'achievements' => '高校運動部の栄養セミナー、個別食事サポートを継続実施。',
                'desired_fee_range' => '15,000円〜 / 60分',
                'message' => '保護者や顧問の先生も実践しやすい食事サポートを行います。',
            ],
            [
                'email' => 'kobayashi.analysis@example.com',
                'name' => '小林 健',
                'kana' => 'こばやし けん',
                'roman_name' => 'Ken Kobayashi',
                'birth_year' => 1989,
                'affiliation' => '札幌ゲーム分析スタジオ',
                'main_prefecture' => '北海道',
                'area' => '札幌・オンライン',
                'sports' => ['バレーボール', 'フットサル'],
                'fields' => ['アナリスト'],
                'degree' => '情報科学学士',
                'qualifications' => '映像分析スペシャリスト',
                'keywords' => '試合分析、映像編集、スカウティング',
                'achievements' => '学生チーム向けに低コストな映像分析フローを導入。',
                'desired_fee_range' => '18,000円〜 / 1試合',
                'message' => '試合映像を、次の練習にすぐ活かせる形に整理します。',
            ],
        ];

        foreach ($coaches as $coach) {
            $user = User::where('email', $coach['email'])->first();
            CoachProfile::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($coach, [
                    'user_id' => $user->id,
                    'available_prefectures' => [$coach['main_prefecture']],
                    'status' => 'approved',
                    'verification_status' => 'verified',
                    'completeness_score' => 92,
                    'photo_path' => in_array($coach['name'], ['佐藤 美咲', '山田 彩'], true) ? 'images/coach-female-editorial.png' : 'images/sample-coach-profile.png',
                    'desired_fee_range' => '応相談',
                    'profile_updated_at' => now(),
                ])
            );
        }

        $additionalCoaches = [
            ['鈴木 一真','Kazuma Suzuki','宮城',['宮城','福島','山形'],'サッカー','競技指導','JFA公認B級コーチ','育成年代の判断力と主体性を伸ばす指導を行います。'],
            ['高橋 奈々','Nana Takahashi','埼玉',['埼玉','東京','千葉'],'バレーボール','トレーニング','JSPO公認コーチ','成長期に合わせた動作を大切にしながら競技力を高めます。'],
            ['伊藤 拓海','Takumi Ito','千葉',['千葉','東京','茨城'],'野球','競技指導','JSBB公認学童コーチ','基礎技術と考える力を両立した練習を設計します。'],
            ['渡辺 由佳','Yuka Watanabe','神奈川',['神奈川','東京','静岡'],'水泳','コンディショニング','日本水泳連盟公認コーチ','フォーム改善と障害予防を一体でサポートします。'],
            ['中村 圭介','Keisuke Nakamura','新潟',['新潟','富山','長野'],'陸上競技','競技指導','日本陸連公認コーチ','走動作を映像とタイムの両面から丁寧に改善します。'],
            ['小川 さくら','Sakura Ogawa','静岡',['静岡','愛知','山梨'],'テニス','メンタル','公認スポーツ心理士','試合で力を発揮するためのルーティンづくりを支援します。'],
            ['加藤 亮','Ryo Kato','京都',['京都','大阪','滋賀'],'ラグビー','トレーニング','NSCA-CSCS','安全性を重視したフィジカル強化を提供します。'],
            ['吉田 美月','Mizuki Yoshida','兵庫',['兵庫','大阪','岡山'],'バドミントン','競技指導','日本スポーツ協会公認コーチ','フットワークとゲーム理解を段階的に伸ばします。'],
            ['山本 智也','Tomoya Yamamoto','広島',['広島','岡山','山口'],'ソフトボール','競技指導','公認ソフトボールコーチ','チームの目標から逆算した練習計画を作ります。'],
            ['松本 結衣','Yui Matsumoto','香川',['香川','徳島','愛媛'],'複数競技','栄養','公認スポーツ栄養士','家庭と連携できる成長期の食事支援が得意です。'],
            ['井上 大輔','Daisuke Inoue','熊本',['熊本','福岡','鹿児島'],'バスケットボール','競技指導','JBA公認C級コーチ','個人技術をチーム戦術につなげる指導を行います。'],
            ['木村 遥','Haruka Kimura','北海道',['北海道','青森'],'スキー','コンディショニング','理学療法士','冬季競技の身体づくりと復帰支援を行います。'],
            ['林 俊介','Shunsuke Hayashi','長野',['長野','群馬','山梨'],'陸上競技','アナリスト','映像分析スペシャリスト','動作データを現場で使える言葉に変えて共有します。'],
            ['清水 愛','Ai Shimizu','福井',['福井','石川','滋賀'],'ハンドボール','リハビリ','アスレティックトレーナー','怪我から競技復帰までチームと伴走します。'],
            ['山口 航平','Kohei Yamaguchi','沖縄',['沖縄'],'サッカー','競技指導','JFA公認C級コーチ','地域の環境に合わせた長期育成を大切にしています。'],
            ['阿部 真由','Mayu Abe','岩手',['岩手','宮城','秋田'],'複数競技','メンタル','認定スポーツメンタルコーチ','選手と指導者のコミュニケーションを整えます。'],
            ['池田 直人','Naoto Ikeda','群馬',['群馬','埼玉','栃木'],'柔道','競技指導','全日本柔道連盟公認指導者','礼節と安全を土台に実戦力を育てます。'],
            ['橋本 梨沙','Risa Hashimoto','鹿児島',['鹿児島','宮崎','熊本'],'バレーボール','トレーニング','健康運動指導士','少人数でも継続できる体力づくりを提案します。'],
        ];
        foreach ($additionalCoaches as $index => [$name,$roman,$prefecture,$available,$sport,$field,$qualification,$message]) {
            $user = User::updateOrCreate(['email' => 'coach'.($index+10).'@example.com'], ['name'=>$name,'password'=>Hash::make('password'),'role'=>'coach','member_type'=>'coach_member','status'=>'approved']);
            CoachProfile::updateOrCreate(['user_id'=>$user->id], [
                'name'=>$name,'kana'=>'こうにん しどうしゃ','roman_name'=>$roman,'birth_year'=>1980+($index%15),'affiliation'=>$prefecture.'スポーツサポート','main_prefecture'=>$prefecture,
                'available_prefectures'=>$available,'area'=>$prefecture.'県内・オンライン','sports'=>[$sport],'fields'=>[$field],'degree'=>'スポーツ科学関連課程修了','qualifications'=>$qualification,
                'keywords'=>$sport.'、育成年代、チーム支援','target_ages'=>'小学生・中学生・高校生','target_levels'=>'初心者・部活動・大会出場','teaching_styles'=>'個人・チーム・オンライン',
                'achievements'=>'学校部活動、地域クラブ、競技団体で継続的な指導実績があります。','request_history'=>'年間指導計画、短期講習、大会前サポートを担当。','message'=>$message,
                'email'=>'coach'.($index+10).'@example.com','phone'=>'090-'.str_pad((string)(1200+$index),4,'0',STR_PAD_LEFT).'-'.str_pad((string)(5600+$index),4,'0',STR_PAD_LEFT),
                'desired_fee_range'=>'応相談','photo_path'=>$index%2 ? 'images/coach-female-editorial.png' : 'images/sample-coach-profile.png','status'=>'approved','verification_status'=>'verified','completeness_score'=>88+($index%10),'show_available_prefectures'=>true,'profile_updated_at'=>now()->subDays($index),
            ]);
        }

        $organizations = [
            [
                'email' => 'aoyama-hs@example.com',
                'name' => '青山高等学校 バスケットボール部',
                'main_prefecture' => '東京',
                'area' => '渋谷・表参道',
                'sport' => 'バスケットボール',
                'target_age' => '高校生',
                'gender' => '男子',
                'member_count' => 28,
                'manager_name' => '高橋 健一',
            ],
            [
                'email' => 'tokai-club@example.com',
                'name' => '東海ジュニアスポーツクラブ',
                'main_prefecture' => '愛知',
                'area' => '名古屋市',
                'sport' => 'ハンドボール',
                'target_age' => '中学生',
                'gender' => '女子',
                'member_count' => 34,
                'manager_name' => '中村 由美',
            ],
            [
                'email' => 'kansai-academy@example.com',
                'name' => '関西アスリートアカデミー',
                'main_prefecture' => '大阪',
                'area' => '北摂・大阪市内',
                'sport' => 'サッカー',
                'target_age' => '小学生・中学生',
                'gender' => '男女',
                'member_count' => 96,
                'manager_name' => '西村 航',
            ],
            [
                'email' => 'fukuoka-city@example.com',
                'name' => '福岡市地域スポーツ推進室',
                'main_prefecture' => '福岡',
                'area' => '福岡市全域',
                'sport' => '複数競技',
                'target_age' => '小学生・中学生',
                'gender' => '男女',
                'member_count' => 180,
                'manager_name' => '松尾 春香',
            ],
        ];

        foreach ($organizations as $organization) {
            $user = User::where('email', $organization['email'])->first();
            $organizationData = $organization;
            unset($organizationData['email']);

            Organization::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($organizationData, [
                    'user_id' => $user->id,
                    'manager_email' => $organization['email'],
                    'manager_phone' => '03-6822-4541',
                    'official_url' => 'https://highclass-inc.com/',
                    'registered_at' => now()->toDateString(),
                    'status' => 'approved',
                    'image_path' => 'images/school-team-practice.png',
                    'introduction' => '地域と学校が連携し、選手が安心して競技を続けられる環境づくりに取り組んでいます。',
                ])
            );
        }

        $additionalOrganizations = [
            ['札幌ユーススポーツセンター','北海道','札幌市','複数競技','中学生・高校生',120],
            ['仙台市立青葉中学校 陸上競技部','宮城','仙台市青葉区','陸上競技','中学生',42],
            ['浦和ジュニアフットボールクラブ','埼玉','さいたま市','サッカー','小学生・中学生',85],
            ['横浜ベイスポーツアカデミー','神奈川','横浜市','バスケットボール','中学生・高校生',68],
            ['信州アスリート育成クラブ','長野','長野市・松本市','陸上競技','中学生・高校生',54],
            ['京都みらい高等学校 バレーボール部','京都','京都市','バレーボール','高校生',31],
            ['神戸地域スポーツ連携会','兵庫','神戸市','複数競技','小学生・中学生',140],
            ['広島瀬戸内スポーツクラブ','広島','広島市','ソフトボール','中学生',46],
            ['高松ジュニアアスリートクラブ','香川','高松市','複数競技','小学生・中学生',72],
            ['熊本ユースバスケットボール協会','熊本','熊本市','バスケットボール','中学生・高校生',93],
            ['鹿児島スポーツ未来プロジェクト','鹿児島','鹿児島市','複数競技','小学生・中学生',160],
            ['那覇市地域部活動推進室','沖縄','那覇市','複数競技','中学生',110],
        ];
        foreach ($additionalOrganizations as $index => [$name,$prefecture,$area,$sport,$target,$members]) {
            $email='organization'.($index+10).'@example.com';
            $user=User::updateOrCreate(['email'=>$email],['name'=>$name,'password'=>Hash::make('password'),'role'=>'organization','member_type'=>'browsing_member','status'=>'approved']);
            Organization::updateOrCreate(['user_id'=>$user->id],[
                'name'=>$name,'main_prefecture'=>$prefecture,'area'=>$area,'sport'=>$sport,'target_age'=>$target,'gender'=>'男女','member_count'=>$members,
                'manager_name'=>'運営責任者 '.($index+1),'manager_email'=>$email,'manager_phone'=>'03-6822-'.str_pad((string)(4600+$index),4,'0',STR_PAD_LEFT),
                'official_url'=>'https://highclass-inc.com/','registered_at'=>now()->subMonths(($index%8)+1)->toDateString(),'status'=>'approved',
                'image_path'=>$index%3===0?'images/track-coaching.png':'images/school-team-practice.png','introduction'=>'学校・地域・保護者と連携し、競技力と人間的成長の両方を支える持続可能な活動を目指しています。',
            ]);
        }

        $jobs = [
            [
                'organization' => '青山高等学校 バスケットボール部',
                'title' => '高校男子バスケットボール部の外部コーチ募集',
                'job_type' => '競技コーチ',
                'prefecture' => '東京',
                'area' => '渋谷区',
                'required_conditions' => '高校生年代の指導経験、またはJBA公認資格をお持ちの方を歓迎します。',
                'target_age' => '高校生',
                'gender' => '男子',
                'sport' => 'バスケットボール',
                'request_frequency' => '週1回',
                'request_style' => '対面',
                'role_description' => '基礎技術、チーム戦術、練習メニュー設計の支援。',
                'detail' => '平日夕方または土曜日午前の練習に参加できる方を探しています。',
                'status' => 'published',
            ],
            [
                'organization' => '東海ジュニアスポーツクラブ',
                'title' => '女子ハンドボールチームのフィジカル強化サポート',
                'job_type' => 'トレーニングコーチ',
                'prefecture' => '愛知',
                'area' => '名古屋市',
                'required_conditions' => '成長期選手のコンディショニングに理解がある方。',
                'target_age' => '中学生',
                'gender' => '女子',
                'sport' => 'ハンドボール',
                'request_frequency' => '月2回',
                'request_style' => '対面',
                'role_description' => '怪我予防、体幹強化、ウォームアップ設計。',
                'detail' => '大会期に向けて3か月単位で継続支援を希望します。',
                'status' => 'published',
            ],
            [
                'organization' => '関西アスリートアカデミー',
                'title' => '育成年代向けメンタルトレーニング講師',
                'job_type' => 'メンタルコーチ',
                'prefecture' => '大阪',
                'area' => '大阪市内',
                'required_conditions' => '小中学生への講義またはワークショップ経験。',
                'target_age' => '小学生・中学生',
                'gender' => '男女',
                'sport' => 'サッカー',
                'request_frequency' => '月1回',
                'request_style' => '対面・オンライン',
                'role_description' => '目標設定、試合前ルーティン、保護者向け説明会。',
                'detail' => '選手が前向きに取り組める参加型プログラムを希望します。',
                'status' => 'published',
            ],
            [
                'organization' => '福岡市地域スポーツ推進室',
                'title' => '地域部活動向けスポーツ栄養セミナー',
                'job_type' => '栄養講師',
                'prefecture' => '福岡',
                'area' => '福岡市',
                'required_conditions' => '管理栄養士またはスポーツ栄養領域の実務経験。',
                'target_age' => '中学生',
                'gender' => '男女',
                'sport' => '複数競技',
                'request_frequency' => '単発',
                'request_style' => '対面',
                'role_description' => '補食、試合期の食事、家庭でできる体づくりの講義。',
                'detail' => '顧問・保護者も参加できる90分セミナーを予定しています。',
                'status' => 'pending_review',
            ],
        ];

        foreach ($jobs as $job) {
            $organization = Organization::where('name', $job['organization'])->first();
            $jobData = $job;
            unset($jobData['organization']);

            Job::updateOrCreate(
                ['title' => $job['title']],
                array_merge($jobData, [
                    'organization_id' => $organization->id,
                    'publish_start_at' => $job['status'] === 'published' ? now()->subDays(7)->toDateString() : null,
                    'publish_end_at' => now()->addMonth()->toDateString(),
                ])
            );
        }

        $jobTypes = ['競技指導者','トレーニングコーチ','メンタルコーチ','コンディショニング','栄養サポート','アナリスト'];
        foreach (Organization::where('status','approved')->get() as $index => $organization) {
            foreach ([0,1] as $offset) {
                $jobType = $jobTypes[($index+$offset)%count($jobTypes)];
                $title = $organization->sport.'部門 '.$jobType.'募集 '.($offset+1);
                Job::updateOrCreate(['title'=>$title],[
                    'organization_id'=>$organization->id,'job_type'=>$jobType,'prefecture'=>$organization->main_prefecture,'area'=>$organization->area,
                    'required_conditions'=>'対象年代への指導経験があり、安全管理と関係者との連携を大切にできる方。関連資格保有者を歓迎します。',
                    'target_age'=>$organization->target_age,'gender'=>$organization->gender,'sport'=>$organization->sport,'request_frequency'=>$offset?'月2回':'週1回',
                    'request_style'=>$offset?'オンライン・対面':'対面','role_description'=>'練習計画の作成、専門指導、活動後の振り返りを担当していただきます。',
                    'detail'=>'選手の発達段階とチーム目標を共有し、顧問・責任者と相談しながら継続的に支援していただく案件です。',
                    'notes'=>'開始時期と具体的な曜日は面談時に相談します。','budget_note'=>'応相談','status'=>'published',
                    'image_path'=>($index+$offset)%3===0?'images/track-coaching.png':'images/school-team-practice.png',
                    'publish_start_at'=>now()->subDays(($index*2)+$offset)->toDateString(),'publish_end_at'=>now()->addDays(30+($index%4)*14)->toDateString(),
                ]);
            }
        }

        $applications = [
            ['job' => '高校男子バスケットボール部の外部コーチ募集', 'coach' => '森 直樹', 'status' => 'organization_review'],
            ['job' => '女子ハンドボールチームのフィジカル強化サポート', 'coach' => '佐藤 美咲', 'status' => 'interview'],
            ['job' => '育成年代向けメンタルトレーニング講師', 'coach' => '田中 達也', 'status' => 'applied'],
        ];

        foreach ($applications as $application) {
            $job = Job::where('title', $application['job'])->first();
            $coach = CoachProfile::where('name', $application['coach'])->first();
            $savedApplication = Application::updateOrCreate(
                ['job_id' => $job->id, 'coach_profile_id' => $coach->id],
                [
                    'message' => '募集内容を拝見し、これまでの経験を活かして支援できると感じ応募しました。',
                    'available_schedule' => '平日夕方、土曜日午前を中心に調整可能です。',
                    'condition_note' => '初回はオンライン打ち合わせも可能です。',
                    'attachment_url' => 'https://highclass-inc.com/',
                    'status' => $application['status'],
                ]
            );
            $savedApplication->statusHistories()->updateOrCreate(
                ['to_status' => $application['status']],
                [
                    'changed_by' => $job->organization->user_id,
                    'from_status' => $application['status'] === 'applied' ? null : 'applied',
                    'note' => $application['status'] === 'interview' ? 'オンライン面談の日程を調整しています。' : 'サンプル選考データです。',
                ]
            );
        }

        $allCoaches = CoachProfile::where('status','approved')->get();
        $liveStatuses = ['applied','organization_review','interview','accepted','completed','rejected'];
        foreach (Job::where('status','published')->take(28)->get() as $index => $job) {
            foreach ([0,1] as $offset) {
                $coach = $allCoaches[($index*2+$offset)%$allCoaches->count()];
                $status = $liveStatuses[($index+$offset)%count($liveStatuses)];
                $application = Application::updateOrCreate(['job_id'=>$job->id,'coach_profile_id'=>$coach->id],[
                    'message'=>'募集内容と活動方針に共感しました。これまでの学校・地域クラブでの経験を活かし、選手と指導者の皆様を支援したいと考えています。',
                    'available_schedule'=>'平日16時以降、土日を中心に調整できます。オンライン事前面談にも対応可能です。','condition_note'=>'活動開始前に目標と役割分担を確認したいです。','status'=>$status,
                ]);
                $application->statusHistories()->updateOrCreate(['to_status'=>'applied'],['changed_by'=>$coach->user_id,'from_status'=>null,'note'=>'案件へ応募しました。']);
                if ($status!=='applied') $application->statusHistories()->updateOrCreate(['to_status'=>$status],['changed_by'=>$job->organization->user_id,'from_status'=>'applied','note'=>$status==='interview'?'初回オンライン面談を調整中です。':'選考状況を更新しました。']);
                if ($status==='completed') Review::updateOrCreate(['application_id'=>$application->id],[
                    'organization_id'=>$job->organization_id,'coach_profile_id'=>$coach->id,'rating'=>4+($index%2),'title'=>'選手に寄り添う丁寧な指導','body'=>'事前のヒアリングから活動後の振り返りまで丁寧で、選手自身が考える時間を大切にしてくださいました。','status'=>'published',
                ]);
            }
        }

        foreach (Organization::where('status','approved')->take(10)->get() as $index => $organization) {
            $coach=$allCoaches[($index+3)%$allCoaches->count()];
            $organization->favoriteCoaches()->syncWithoutDetaching([$coach->id,$allCoaches[($index+7)%$allCoaches->count()]->id]);
            Offer::updateOrCreate(['organization_id'=>$organization->id,'coach_profile_id'=>$coach->id,'subject'=>'来学期の専門指導について'],[
                'job_id'=>$organization->jobs()->where('status','published')->value('id'),'message'=>'プロフィールを拝見し、当団体の選手への専門指導をお願いしたくご連絡しました。まずは活動方針と日程についてお話しできれば幸いです。',
                'proposed_schedule'=>'2026年10月以降、月2回程度','status'=>$index%4===0?'accepted':($index%4===1?'declined':'sent'),'responded_at'=>$index%4<2?now()->subDays($index+1):null,
            ]);
        }

        $organizationUser = User::where('email', 'aoyama-hs@example.com')->first();
        $sampleCoach = CoachProfile::where('name', '森 直樹')->first();
        Inquiry::updateOrCreate(
            ['user_id' => $organizationUser->id, 'subject' => '春季大会に向けた短期指導の相談'],
            [
                'coach_profile_id' => $sampleCoach->id,
                'category' => 'consultation',
                'body' => '高校男子バスケットボール部です。春季大会までの3か月間、月2回程度の技術指導について相談したいです。',
                'status' => 'resolved',
                'admin_reply' => 'ご相談ありがとうございます。指導者へ活動条件を確認し、担当よりメールで日程候補をご連絡します。',
                'replied_by' => $admin->id,
                'replied_at' => now()->subDay(),
            ]
        );

        $articles = [
            ['coach-interview-regional-sports','interview','地域の部活動で、専門家だからできること','競技力だけではなく、選手が自ら考える環境をつくる。地域指導に取り組むコーチへのインタビューです。','images/sports-analysis-interview.png'],
            ['safe-training-for-youth','knowledge','成長期の選手を守るトレーニング設計','学校と外部指導者が共有しておきたい、負荷管理と怪我予防の基本を解説します。','images/track-coaching.png'],
            ['school-community-case-study','case_study','学校と地域クラブがつくった新しい指導体制','顧問、保護者、外部コーチが役割を整理し、持続可能な活動へ移行した事例を紹介します。','images/school-team-practice.png'],
            ['sports-nutrition-practice','knowledge','部活動で実践できるスポーツ栄養','特別な設備がなくても始められる、成長期の補食と試合期の食事づくり。','images/coach-female-editorial.png'],
            ['video-analysis-first-step','knowledge','映像分析を次の練習につなげる方法','撮影するだけで終わらせず、選手の理解と具体的な練習改善につなげる手順を整理します。','images/sports-analysis-interview.png'],
            ['mental-routine-before-game','interview','試合前の不安を力に変えるルーティン','メンタルコーチが、育成年代に適した目標設定と試合前準備について語ります。','images/track-coaching.png'],
        ];
        foreach($articles as $index=>[$slug,$category,$title,$excerpt,$image]) Article::updateOrCreate(['slug'=>$slug],[
            'author_id'=>$admin->id,'title'=>$title,'category'=>$category,'excerpt'=>$excerpt,
            'body'=>$excerpt."\n\n地域のスポーツ現場では、専門知識と同じくらい、選手・顧問・保護者の間で目的を共有することが大切です。まず現状を丁寧に確認し、無理なく続けられる小さな改善から始めます。\n\nHigh Class Matchingでは、指導者の資格、実績、対応地域を確認し、学校・団体の課題に合う出会いを支援しています。現場で得られた知見を継続的に発信し、より安全で豊かなスポーツ環境づくりにつなげます。",
            'cover_image_path'=>$image,'status'=>'published','published_at'=>now()->subDays($index*9+2),
        ]);

        $masters = [
            'sport'=>['サッカー','バスケットボール','バレーボール','野球','陸上競技','テニス','水泳','ハンドボール','ラグビー','柔道','複数競技'],
            'field'=>['競技指導','メンタル','トレーニング','リハビリ','コンディショニング','栄養','アナリスト'],
            'job_type'=>['競技指導者','メンタルコーチ','トレーニングコーチ','栄養サポート','アナリスト'],
            'target_age'=>['小学生','中学生','高校生','大学生','社会人'],
            'request_style'=>['対面','オンライン','大会帯同','相談ベース'],
        ];
        foreach($masters as $type=>$values) foreach($values as $order=>$value) MatchingMaster::updateOrCreate(['type'=>$type,'value'=>$value],['sort_order'=>$order,'is_active'=>true]);

        DB::table('notifications')->updateOrInsert(
            ['id' => '11111111-1111-4111-8111-111111111111'],
            [
                'type' => 'App\\Notifications\\MatchingActivityNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $organizationUser->id,
                'data' => json_encode([
                    'title' => '問い合わせへの返信',
                    'message' => '「春季大会に向けた短期指導の相談」に運営から返信が届きました。',
                    'url' => '/inquiries/'.Inquiry::where('subject', '春季大会に向けた短期指導の相談')->value('id'),
                ], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]
        );

        DB::table('notifications')->updateOrInsert(
            ['id' => '22222222-2222-4222-8222-222222222222'],
            [
                'type' => 'App\\Notifications\\MatchingActivityNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $sampleCoach->user_id,
                'data' => json_encode([
                    'title' => '応募ステータスが更新されました',
                    'message' => '応募案件が団体確認中になりました。',
                    'url' => '/dashboard',
                ], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ]
        );

        Inquiry::updateOrCreate(
            ['user_id' => $sampleCoach->user_id, 'subject' => 'プロフィール公開項目について'],
            [
                'category' => 'account',
                'body' => '依頼実績を非公開にしたまま、指導実績だけを掲載できますか。',
                'status' => 'in_progress',
                'admin_note' => '公開設定機能をご案内する。',
            ]
        );
    }
}

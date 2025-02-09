# インターネットTV

## STEP1 - テーブル設計をする。

テーブル：channels
| カラム名  | データ型      | NULL | キー        | 初期値 | AUTO INCREMENT |
|----------|------------|------|------------|------|---------------|
| id       | INT        |      | PRIMARY    |      | YES           |
| name     | VARCHAR(20)|      | UNIQUE     |      |               |

（中間 - channels, episodes）テーブル：schedules
| カラム名   | データ型   | NULL | キー              | 初期値 | AUTO INCREMENT |
|-----------|----------|------|----------------|------|---------------|
| id        | INT      |      | PRIMARY        |      | YES           |
| channel_id| INT      |      | UNIQUE, FOREIGN |      |               |
| episode_id| INT      |      | UNIQUE, FOREIGN |      |               |
| start_time| TIMESTAMP|      | UNIQUE, FOREIGN |      |               |
| end_time  | TIMESTAMP|      |                |      |               |
| view_count| INT      |      |                |      |               |

テーブル：genres
| カラム名 | データ型    | NULL | キー     | 初期値 | AUTO INCREMENT |
|---------|----------|------|--------|------|---------------|
| id      | INT      |      | PRIMARY |      | YES           |
| name    | VARCHAR(20)|      | UNIQUE  |      |               |

（中間 - genres, programs）テーブル：program_genres
| カラム名   | データ型  | NULL | キー              | 初期値 | AUTO INCREMENT |
|-----------|---------|------|----------------|------|---------------|
| genre_id  | INT     |      | PRIMARY, FOREIGN |      |               |
| program_id| INT     |      | PRIMARY, FOREIGN |      |               |

テーブル：programs
| カラム名   | データ型     | NULL | キー     | 初期値 | AUTO INCREMENT |
|----------|----------|------|--------|------|---------------|
| id       | INT      |      | PRIMARY |      | YES           |
| name     | VARCHAR(20) |      |        |      |               |
| genre_id | INT      |      | FOREIGN |      |               |
| description | VARCHAR(200) | YES  |        | ' '  |               |

テーブル：series
| カラム名  | データ型     | NULL | キー     | 初期値 | AUTO INCREMENT |
|----------|----------|------|--------|------|---------------|
| id       | INT      |      | PRIMARY |      | YES           |
| name     | VARCHAR(20) |      |        |      |               |
| program_id | INT   |      | FOREIGN |      |               |

テーブル：seasons
| カラム名      | データ型  | NULL | キー     | 初期値 | AUTO INCREMENT |
|------------|---------|------|--------|------|---------------|
| id         | INT     |      | PRIMARY |      | YES           |
| season_number | CHAR(5) |      |        |      |               |
| program_id | INT     |      | FOREIGN |      |               |
| series_id  | INT     |      | FOREIGN |      |               |

テーブル：episodes
| カラム名   | データ型      | NULL | キー     | 初期値 | AUTO INCREMENT |
|----------|------------|------|--------|------|---------------|
| id       | INT        |      | PRIMARY |      | YES           |
| name     | VARCHAR(40)|      |        |      |               |
| program_id | INT      |      | FOREIGN |      |               |
| series_id  | INT      |      | FOREIGN |      |               |
| season_id  | INT      |      | FOREIGN |      |               |
| description| VARCHAR(200)| YES |        | ' '  |               |



## STEP2 - テーブル設計をする。
1. データベースを構築
MySQLを起動 -> MySQLにログイン -> CREATE DATABASE データベース名

2. ステップ1で設計したテーブルを構築

**WorkBenchを使ってER図からテーブルを作成**
```sql
CREATE TABLE IF NOT EXISTS internet_tv.channels (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  UNIQUE INDEX channel_name_UNIQUE (name ASC) VISIBLE,
  PRIMARY KEY (id))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS internet_tv.programs (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  is_series TINYINT(1) NOT NULL DEFAULT 0,
  description VARCHAR(255) NULL DEFAULT ' ',
  PRIMARY KEY (id))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS internet_tv.seasons (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  season_number SMALLINT UNSIGNED NOT NULL,
  program_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  INDEX fk_seasons_program_idx (program_id ASC) VISIBLE,
  CONSTRAINT fk_seasons_program
    FOREIGN KEY (program_id)
    REFERENCES internet_tv.programs (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS internet_tv.episodes (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  season_id INT(10) UNSIGNED NOT NULL,
  description VARCHAR(255) NULL DEFAULT ' ',
  PRIMARY KEY (id),
  INDEX fk_episodes_seasons_idx (season_id ASC) VISIBLE,
  CONSTRAINT fk_episodes_seasons
    FOREIGN KEY (season_id)
    REFERENCES internet_tv.seasons (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS internet_tv.schedules (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  channel_id INT(10) UNSIGNED NOT NULL,
  episode_id INT(10) UNSIGNED NULL DEFAULT NULL,
  start_time TIMESTAMP NOT NULL,
  end_time TIMESTAMP NOT NULL,
  view_count BIGINT(20) NOT NULL DEFAULT 0,
  program_id INT(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (id),
  INDEX fk_schedules_channels_idx (channel_id ASC) VISIBLE,
  INDEX fk_schedules_episodes_idx (episode_id ASC) VISIBLE,
  UNIQUE INDEX channel_episode_start_UNIQUE (channel_id ASC, episode_id ASC, start_time ASC) VISIBLE,
  INDEX fk_schedules_programs_idx (program_id ASC) VISIBLE,
  CONSTRAINT fk_schedules_channels
    FOREIGN KEY (channel_id)
    REFERENCES internet_tv.channels (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT fk_schedules_episodes
    FOREIGN KEY (episode_id)
    REFERENCES internet_tv.episodes (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT fk_schedules_programs
    FOREIGN KEY (program_id)
    REFERENCES internet_tv.programs (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS internet_tv.genres (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX genre_name_UNIQUE (name ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS internet_tv.program_genres (
  genres_id INT(10) UNSIGNED NOT NULL,
  program_id INT(10) UNSIGNED NOT NULL,
  INDEX program_id_idx (program_id ASC) VISIBLE,
  PRIMARY KEY (genres_id, program_id),
  CONSTRAINT fk_program_genres_genres
    FOREIGN KEY (genres_id)
    REFERENCES internet_tv.genres (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT fk_program_genres_programs
    FOREIGN KEY (program_id)
    REFERENCES internet_tv.programs (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;
```



3. サンプルデータを入れる。（ChatGPTを利用）
```sql
-- チャンネルデータ（10チャンネル）
INSERT INTO channels (id, name) VALUES
(1, 'ABEMA SPECIAL'),
(2, 'ABEMA NEWS'),
(3, 'ABEMA DRAMA'),
(4, 'ABEMA SPORTS'),
(5, 'ABEMA ANIME'),
(6, 'ABEMA VARIETY'),
(7, 'ABEMA DOCUMENTARY'),
(8, 'ABEMA MUSIC'),
(9, 'ABEMA MOVIES'),
(10, 'ABEMA KIDS');

-- ジャンルデータ（10ジャンル）
INSERT INTO genres (id, name) VALUES
(1, 'ニュース'),
(2, 'アニメ'),
(3, 'ドラマ'),
(4, 'スポーツ'),
(5, 'バラエティ'),
(6, 'ドキュメンタリー'),
(7, '音楽'),
(8, '映画'),
(9, 'キッズ'),
(10, 'ゲーム');

-- ジャンル、番組の中間テーブルデータ
INSERT INTO program_genres (genres_id, program_id) VALUES
(1, 1), -- ニュース -> ABEMA NEWS SHOW
(2, 2), -- アニメ -> 転生したらスライムだった件
(3, 3), -- ドラマ -> ABEMAオリジナルドラマ
(4, 4), -- スポーツ -> サッカーW杯特集
(5, 5), -- バラエティ -> お笑いライブ
(6, 6), -- ドキュメンタリー -> 歴史探訪
(7, 7), -- 音楽 -> ABEMA MUSIC NIGHT
(8, 8), -- 映画 -> アクションヒーロー
(9, 9), -- キッズ -> アニメキャラショー
(2, 10), -- アニメ -> 鬼滅の刃
(2, 11), -- アニメ -> 呪術廻戦
(3, 12), -- ドラマ -> 恋愛ストーリー
(8, 13), -- 映画 -> サスペンススリラー
(5, 14), -- バラエティ -> 人気YouTuber特集
(1, 15), -- ニュース -> 経済特集
(4, 16), -- スポーツ -> プロ野球特集
(6, 17), -- ドキュメンタリー -> 未来技術
(2, 18), -- アニメ -> ワンピース
(2, 19), -- アニメ -> ドラゴンボール
(8, 20), -- 映画 -> ホラー特集
(5, 21), -- バラエティ -> 芸能界の裏話
(3, 22), -- ドラマ -> 刑事ドラマ特集
(7, 23), -- 音楽 -> K-POPライブ
(4, 24), -- スポーツ -> ボクシング特集
(2, 25), -- アニメ -> リゼロ
(2, 26), -- アニメ -> SAO
(8, 27), -- 映画 -> ファンタジー映画特集
(5, 28), -- バラエティ -> 大食いチャレンジ
(1, 29), -- ニュース -> 国際情勢
(3, 30); -- ドラマ -> 法廷サスペンス

-- 番組データ（30番組）
INSERT INTO programs (id, name, is_series, description) VALUES
(1, 'ABEMA NEWS SHOW', 0, 'ABEMAのニュース番組'),
(2, 'アニメ「転生したらスライムだった件」', 1, '人気の異世界アニメ'),
(3, 'ABEMAオリジナルドラマ', 1, 'ABEMAオリジナルの恋愛ドラマ'),
(4, 'スポーツ特集 - サッカーW杯', 0, 'サッカーW杯の特集'),
(5, 'バラエティ「お笑いライブ」', 1, '人気芸人のお笑いライブ'),
(6, 'ドキュメンタリー「歴史探訪」', 1, '歴史的な事件を振り返る'),
(7, 'ABEMA MUSIC NIGHT', 0, '最新の音楽ライブ配信'),
(8, '映画「アクションヒーロー」', 0, '人気アクション映画'),
(9, 'キッズ番組「アニメキャラショー」', 1, '子供向けアニメ番組'),
(10, 'アニメ「鬼滅の刃」', 1, '大ヒットアニメ'),
(11, 'アニメ「呪術廻戦」', 1, '人気のバトルアニメ'),
(12, 'ドラマ「恋愛ストーリー」', 1, '話題のラブストーリー'),
(13, '映画「サスペンススリラー」', 0, 'サスペンス映画特集'),
(14, 'バラエティ「人気YouTuber特集」', 1, '有名YouTuberが出演'),
(15, 'ニュース「経済特集」', 0, '最新の経済情報を特集'),
(16, 'スポーツ「プロ野球特集」', 0, 'プロ野球の試合ハイライト'),
(17, 'ドキュメンタリー「未来技術」', 1, '最新の技術動向を解説'),
(18, 'アニメ「ワンピース」', 1, '長寿アニメ「ワンピース」'),
(19, 'アニメ「ドラゴンボール」', 1, '名作バトルアニメ'),
(20, '映画「ホラー特集」', 0, 'ホラー映画の特集'),
(21, 'バラエティ「芸能界の裏話」', 1, '芸能人の暴露トーク'),
(22, 'ドラマ「刑事ドラマ特集」', 1, 'サスペンス刑事ドラマ'),
(23, '音楽番組「K-POPライブ」', 0, 'K-POPアーティストの特集'),
(24, 'スポーツ「ボクシング特集」', 0, 'ボクシングの名試合を振り返る'),
(25, 'アニメ「リゼロ」', 1, '異世界アニメの名作'),
(26, 'アニメ「SAO」', 1, 'オンラインゲームを舞台にしたアニメ'),
(27, '映画「ファンタジー映画特集」', 0, 'ファンタジー映画の特集'),
(28, 'バラエティ「大食いチャレンジ」', 1, '大食いタレントが挑戦'),
(29, 'ニュース「国際情勢」', 0, '世界のニュースを特集'),
(30, 'ドラマ「法廷サスペンス」', 1, '法廷ドラマの特集');

-- シーズンデータ（シリーズものの番組用）
INSERT INTO seasons (id, season_number, program_id) VALUES
(1, 1, 2), (2, 2, 2),
(3, 1, 3), (4, 1, 5),
(5, 1, 6), (6, 1, 9),
(7, 1, 10), (8, 2, 10),
(9, 1, 11), (10, 1, 12),
(11, 1, 14), (12, 1, 17),
(13, 1, 18), (14, 1, 19),
(15, 1, 21), (16, 1, 22),
(17, 1, 25), (18, 1, 26),
(19, 1, 28), (20, 1, 30);

-- エピソードデータ（50件）
INSERT INTO episodes (id, name, season_id, description) VALUES
(1, '転スラ 第1話', 1, '異世界転生の始まり'),
(2, '転スラ 第2話', 1, 'スライムの冒険'),
(3, '転スラ 第3話', 1, '仲間との出会い'),
(4, '鬼滅の刃 第1話', 7, '鬼との戦いが始まる'),
(5, '鬼滅の刃 第2話', 7, '修行の日々'),
(6, '鬼滅の刃 第3話', 7, '最初の任務'),
(7, 'ワンピース 第1話', 13, '海賊王への夢'),
(8, 'ワンピース 第2話', 13, '仲間との出会い'),
(9, 'ワンピース 第3話', 13, '最初の冒険'),
(10, 'SAO 第1話', 18, 'ゲームの世界に閉じ込められる'),
(11, 'SAO 第2話', 18, '最初のボス戦'),
(12, 'SAO 第3話', 18, '新たな仲間'),
(13, '大食いチャレンジ 第1話', 19, '超巨大ラーメン挑戦'),
(14, '大食いチャレンジ 第2話', 19, 'カレー100皿に挑戦'),
(15, '大食いチャレンジ 第3話', 19, '世界最大のバーガー'),
(16, 'ドラマ「恋愛ストーリー」第1話', 10, '出会いの始まり'),
(17, 'ドラマ「恋愛ストーリー」第2話', 10, '急接近する二人'),
(18, 'ドラマ「恋愛ストーリー」第3話', 10, '三角関係勃発'),
(19, '刑事ドラマ 第1話', 16, '初めての事件'),
(20, '刑事ドラマ 第2話', 16, '謎が深まる'),
(21, '刑事ドラマ 第3話', 16, '真実が明らかに'),
(22, '歴史探訪 第1話', 5, '戦国時代の英雄'),
(23, '歴史探訪 第2話', 5, '幕末の志士たち'),
(24, '歴史探訪 第3話', 5, '近代日本の成り立ち'),
(25, 'K-POPライブ 第1回', 7, 'BTSライブ特集'),
(26, 'K-POPライブ 第2回', 7, 'BLACKPINKスペシャル'),
(27, 'K-POPライブ 第3回', 7, 'TWICEの魅力'),
(28, 'サッカーW杯特集 第1回', 4, 'グループステージ開幕'),
(29, 'サッカーW杯特集 第2回', 4, '決勝トーナメント進出チーム'),
(30, 'サッカーW杯特集 第3回', 4, '決勝戦の展望'),
(31, '未来技術 第1話', 12, 'AIの進化'),
(32, '未来技術 第2話', 12, '宇宙開発の未来'),
(33, '未来技術 第3話', 12, '自動運転技術'),
(34, 'ホラー映画特集 第1回', 20, 'ゾンビ映画の歴史'),
(35, 'ホラー映画特集 第2回', 20, '最恐ホラー映画ランキング'),
(36, 'ホラー映画特集 第3回', 20, 'ホラー映画の裏側'),
(37, '芸能界の裏話 第1回', 15, 'スキャンダルの裏側'),
(38, '芸能界の裏話 第2回', 15, '引退した芸能人の今'),
(39, '芸能界の裏話 第3回', 15, '業界のタブー'),
(40, '経済特集 第1回', 9, '株価の変動とその影響'),
(41, '経済特集 第2回', 9, '仮想通貨の未来'),
(42, '経済特集 第3回', 9, '日本経済の行方'),
(43, 'プロ野球特集 第1回', 14, 'シーズン前の展望'),
(44, 'プロ野球特集 第2回', 14, '注目の選手インタビュー'),
(45, 'プロ野球特集 第3回', 14, 'リーグ優勝争い'),
(46, 'ボクシング特集 第1回', 17, '伝説の試合振り返り'),
(47, 'ボクシング特集 第2回', 17, '新世代の王者たち'),
(48, 'ボクシング特集 第3回', 17, 'ボクシングのルール解説'),
(49, 'ファンタジー映画特集 第1回', 20, 'ロードオブザリング徹底解説'),
(50, 'ファンタジー映画特集 第2回', 20, 'ハリーポッターの魅力');

-- スケジュールデータ（53件）
INSERT INTO schedules (id, channel_id, episode_id, start_time, end_time, view_count, program_id) VALUES
(1, 2, 1, '2025-02-10 12:00:00', '2025-02-10 12:30:00', 50000, 2),
(2, 5, 2, '2025-02-10 20:00:00', '2025-02-10 20:30:00', 75000, 2),
(3, 5, 3, '2025-02-11 20:00:00', '2025-02-11 20:30:00', 60000, 2),
(4, 3, 4, '2025-02-12 22:00:00', '2025-02-12 22:45:00', 45000, 10),
(5, 7, 5, '2025-02-13 18:00:00', '2025-02-13 18:30:00', 70000, 10),
(6, 1, 6, '2025-02-14 14:00:00', '2025-02-14 14:30:00', 80000, 10),
(7, 4, 7, '2025-02-15 16:00:00', '2025-02-15 16:30:00', 55000, 18),
(8, 8, 8, '2025-02-16 19:00:00', '2025-02-16 19:30:00', 72000,18),
(9, 6, 9, '2025-02-17 21:00:00', '2025-02-17 21:30:00', 65000, 18),
(10, 10, 10, '2025-02-18 23:00:00', '2025-02-18 23:30:00', 48000, 26),
(11, 2, 11, '2025-02-19 12:00:00', '2025-02-19 12:30:00', 51000, 26),
(12, 5, 12, '2025-02-20 20:00:00', '2025-02-20 20:30:00', 76000, 26),
(13, 4, 13, '2025-02-21 14:00:00', '2025-02-21 14:30:00', 62000, 28),
(14, 9, 14, '2025-02-22 18:00:00', '2025-02-22 18:30:00', 73000, 28),
(15, 3, 15, '2025-02-23 21:00:00', '2025-02-23 21:30:00', 54000, 28),
(16, 7, 16, '2025-02-24 23:00:00', '2025-02-24 23:30:00', 61000, 12),
(17, 1, 17, '2025-02-25 19:00:00', '2025-02-25 19:30:00', 66000, 12),
(18, 6, 18, '2025-02-26 20:00:00', '2025-02-26 20:30:00', 69000, 12),
(19, 10, 19, '2025-02-27 22:00:00', '2025-02-27 22:30:00', 58000, 22),
(20, 2, 20, '2025-02-28 17:00:00', '2025-02-28 17:30:00', 75000, 22),
(21, 5, 21, '2025-03-01 14:00:00', '2025-03-01 14:30:00', 72000, 22),
(22, 8, 22, '2025-03-02 19:00:00', '2025-03-02 19:30:00', 68000, 6),
(23, 7, 23, '2025-03-03 21:00:00', '2025-03-03 21:30:00', 63000, 6),
(24, 1, 24, '2025-03-04 23:00:00', '2025-03-04 23:30:00', 65000, 6),
(25, 3, 25, '2025-03-05 19:00:00', '2025-03-05 19:30:00', 69000, 23),
(26, 5, 26, '2025-03-06 14:00:00', '2025-03-06 14:30:00', 70000, 23),
(27, 8, 27, '2025-03-07 19:00:00', '2025-03-07 19:30:00', 68000, 23),
(28, 7, 28, '2025-03-08 21:00:00', '2025-03-08 21:30:00', 64000, 4),
(29, 1, 29, '2025-03-09 23:00:00', '2025-03-09 23:30:00', 65000, 4),
(30, 3, 30, '2025-03-10 19:00:00', '2025-03-10 19:30:00', 69000, 4),
(31, 2, 31, '2025-03-11 12:00:00', '2025-03-11 12:30:00', 51000, 17),
(32, 6, 32, '2025-03-12 20:00:00', '2025-03-12 20:30:00', 76000, 17),
(33, 9, 33, '2025-03-13 14:00:00', '2025-03-13 14:30:00', 62000, 17),
(34, 4, 34, '2025-03-14 18:00:00', '2025-03-14 18:30:00', 73000, 20),
(35, 10, 35, '2025-03-15 21:00:00', '2025-03-15 21:30:00', 54000, 20),
(36, 8, 36, '2025-03-16 23:00:00', '2025-03-16 23:30:00', 61000, 20),
(37, 7, 37, '2025-03-17 19:00:00', '2025-03-17 19:30:00', 66000, 21),
(38, 1, 38, '2025-03-18 20:00:00', '2025-03-18 20:30:00', 69000, 21),
(39, 3, 39, '2025-03-19 22:00:00', '2025-03-19 22:30:00', 58000, 21),
(40, 2, 40, '2025-03-20 17:00:00', '2025-03-20 17:30:00', 75000, 15),
(41, 5, 41, '2025-03-21 14:00:00', '2025-03-21 14:30:00', 72000, 15),
(42, 8, 42, '2025-03-22 19:00:00', '2025-03-22 19:30:00', 68000, 15),
(43, 7, 43, '2025-03-23 21:00:00', '2025-03-23 21:30:00', 63000, 16),
(44, 1, 44, '2025-03-24 23:00:00', '2025-03-24 23:30:00', 65000, 16),
(45, 3, 45, '2025-03-25 19:00:00', '2025-03-25 19:30:00', 69000, 16),
(46, 2, 46, '2025-03-26 12:00:00', '2025-03-26 12:30:00', 51000, 24),
(47, 6, 47, '2025-03-27 20:00:00', '2025-03-27 20:30:00', 76000, 24),
(48, 9, 48, '2025-03-28 14:00:00', '2025-03-28 14:30:00', 62000, 24),
(49, 4, 49, '2025-03-29 18:00:00', '2025-03-29 18:30:00', 73000, 27),
(50, 10, 50, '2025-03-30 21:00:00', '2025-03-30 21:30:00', 54000, 27),
(51, 1, 37, '2025-02-09 18:00:00', '2025-03-30 18:30:00', 64000, 21),
(52, 3, 38, '2025-02-09 20:00:00', '2025-03-30 20:30:00', 67000, 21),
(53, 1, 39, '2025-02-09 23:00:00', '2025-03-30 23:30:00', 74000, 21);
```

## STEP 3　
（ビュー作成は検討）
1. よく見られているエピソードを知りたいです。エピソード視聴数トップ3のエピソードタイトルと視聴数を取得してください。
```sql
mysql> SELECT e.id, e.name, SUM(s.view_count) AS total_views
    -> FROM schedules s
    -> JOIN episodes e ON s.episode_id = e.id
    -> GROUP BY e.id, e.name
    -> ORDER BY total_views DESC
    -> LIMIT 3;
```
2. よく見られているエピソードの番組情報やシーズン情報も合わせて知りたいです。エピソード視聴数トップ3の番組タイトル、シーズン数、エピソード数、エピソードタイトル、視聴数を取得してください。
```sql
mysql> SELECT e.id AS episode_id, p.name AS program_title, se.season_number, e.name AS episode_title, SUM(s.view_count) AS total_views
    -> FROM schedules s
    -> JOIN episodes e ON s.episode_id = e.id
    -> JOIN seasons se ON e.season_id = se.id
    -> JOIN programs p ON se.program_id = p.id
    -> GROUP BY e.id, p.name, se.season_number, e.name
    -> ORDER BY total_views DESC
    -> LIMIT 3;
```

3. 本日の番組表を表示するために、本日、どのチャンネルの、何時から、何の番組が放送されるのかを知りたいです。本日放送される全ての番組に対して、チャンネル名、放送開始時刻(日付+時間)、放送終了時刻、シーズン数、エピソード数、エピソードタイトル、エピソード詳細を取得してください。なお、番組の開始時刻が本日のものを本日方法される番組とみなすものとします。
```sql
mysql> SELECT ch.name AS channel_name, s.start_time, s.end_time, se.season_number, e.id AS episode_number, e.name AS episode_title, e.description AS episode_description
    -> FROM schedules s
    -> JOIN channels ch ON s.channel_id = ch.id
    -> JOIN episodes e ON s.episode_id = e.id
    -> JOIN seasons se ON e.season_id = se.id
    -> WHERE DATE(s.start_time) = CURDATE()
    -> ORDER BY s.start_time;
```

4. ドラマというチャンネルがあったとして、ドラマのチャンネルの番組表を表示するために、本日から一週間分、何日の何時から何の番組が放送されるのかを知りたいです。ドラマのチャンネルに対して、放送開始時刻、放送終了時刻、シーズン数、エピソード数、エピソードタイトル、エピソード詳細を本日から一週間分取得してください。
```sql
mysql> SELECT s.start_time, s.end_time, se.season_number, e.id AS episode_number, e.name AS episode_title, e.description AS episode_description
    -> FROM schedules s
    -> JOIN channels ch ON s.channel_id = ch.id
    -> JOIN episodes e ON s.episode_id = e.id
    -> JOIN seasons se ON e.season_id = se.id
    -> WHERE ch.name = 'ABEMA DRAMA'
    -> AND DATE(s.start_time) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    -> ORDER BY s.start_time;
```

5. (advanced) 直近一週間で最も見られた番組が知りたいです。直近一週間に放送された番組の中で、エピソード視聴数合計トップ2の番組に対して、番組タイトル、視聴数を取得してください
```sql
mysql> SELECT p.name AS program_title, SUM(s.view_count) AS total_views
    -> FROM schedules s
    -> JOIN episodes e ON s.episode_id = e.id
    -> JOIN seasons se ON e.season_id = se.id
    -> JOIN programs p ON se.program_id = p.id
    -> WHERE DATE(s.start_time) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()
    -> GROUP BY p.id, p.name
    -> ORDER BY total_views DESC
    -> LIMIT 2;
```

6. (advanced) ジャンルごとの番組の視聴数ランキングを知りたいです。番組の視聴数ランキングはエピソードの平均視聴数ランキングとします。ジャンルごとに視聴数トップの番組に対して、ジャンル名、番組タイトル、エピソード平均視聴数を取得してください。
```sql
SELECT genre_name, program_title, avg_views
FROM (
    SELECT g.name AS genre_name, 
           p.name AS program_title, 
           AVG(s.view_count) AS avg_views
    FROM schedules s
    JOIN episodes e ON s.episode_id = e.id
    JOIN seasons se ON e.season_id = se.id
    JOIN programs p ON se.program_id = p.id
    JOIN program_genres pg ON p.id = pg.program_id
    JOIN genres g ON pg.genres_id = g.id
    GROUP BY g.name, p.name
) AS genre_ranked
WHERE (genre_name, avg_views) IN (
    SELECT genre_name, MAX(avg_views)
    FROM (
        SELECT g.name AS genre_name, 
               p.name AS program_title, 
               AVG(s.view_count) AS avg_views
        FROM schedules s
        JOIN episodes e ON s.episode_id = e.id
        JOIN seasons se ON e.season_id = se.id
        JOIN programs p ON se.program_id = p.id
        JOIN program_genres pg ON p.id = pg.program_id
        JOIN genres g ON pg.genres_id = g.id
        GROUP BY g.name, p.name
    ) AS temp
    GROUP BY genre_name
)
ORDER BY avg_views DESC;
```
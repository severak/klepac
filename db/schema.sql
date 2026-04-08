-- Adminer 4.7.5 SQLite 3 dump

DROP TABLE IF EXISTS "feeds";
CREATE TABLE "feeds" (
                         "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                         "key" text NOT NULL,
                         "url" text NOT NULL,
                         "link" text NOT NULL,
                         "feed_title" integer NOT NULL,
                         "feed_description" integer NOT NULL
);


DROP TABLE IF EXISTS "posts";
CREATE TABLE "posts" (
                         "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                         "feed_key" text NOT NULL,
                         "title" text NOT NULL,
                         "description" text NOT NULL,
                         "timestamp" integer NOT NULL,
                         "link" text NOT NULL,
                         "image_url" text NOT NULL,
                         "audio_url" text NOT NULL
);



--
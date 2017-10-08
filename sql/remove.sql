SET foreign_key_checks = 0;

DROP TABLE IF EXISTS track;
DROP TABLE IF EXISTS album;
DROP TABLE IF EXISTS artist;
DROP TABLE IF EXISTS playlist_track;
DROP TABLE IF EXISTS playlist;
DROP TABLE IF EXISTS playlist_party;
DROP TABLE IF EXISTS setting;
DROP TABLE IF EXISTS remote;
DROP PROCEDURE IF EXISTS InsertTrack;
DROP PROCEDURE IF EXISTS MoveTrackInPlaylist;

SET foreign_key_checks = 1;

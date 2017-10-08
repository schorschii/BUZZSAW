CREATE PROCEDURE `InsertTrack`(`insert_track` TEXT, `insert_album` TEXT, `insert_artist` TEXT, `insert_path` TEXT, `insert_track_number` INT, `insert_cover` TEXT) BEGIN

DECLARE artist_check INT;
DECLARE album_check INT;

SELECT id INTO artist_check FROM artist ar
WHERE ar.title = insert_artist
LIMIT 1;

SELECT al.id INTO album_check FROM album al
INNER JOIN artist ar ON al.artist_id = ar.id
WHERE ar.title = insert_artist
AND al.title = insert_album
LIMIT 1;

IF artist_check IS NULL THEN
	INSERT INTO artist (title) VALUES (insert_artist);
    SELECT LAST_INSERT_ID() INTO artist_check;
END IF;
IF album_check IS NULL THEN
	INSERT INTO album (title, artist_id) VALUES (insert_album, artist_check);
    SELECT LAST_INSERT_ID() INTO album_check;
END IF;

INSERT INTO track (title, artist_id, album_id, path, track_number, cover) VALUES (insert_track, artist_check, album_check, insert_path, insert_track_number, insert_cover);

END;

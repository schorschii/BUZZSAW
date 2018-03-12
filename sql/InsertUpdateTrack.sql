CREATE PROCEDURE `InsertUpdateTrack`(`insert_track` TEXT, `insert_album` TEXT, `insert_artist` TEXT, `insert_path` TEXT, `insert_track_number` INT, `insert_cover` TEXT, `insert_duration` INT, `insert_length` BIGINT, `insert_genre` TEXT)

BEGIN

DECLARE artist_check INT;
DECLARE album_check INT;
DECLARE track_check INT;

SELECT id INTO artist_check FROM artist ar
WHERE ar.title = insert_artist
LIMIT 1;

SELECT al.id INTO album_check FROM album al
INNER JOIN artist ar ON al.artist_id = ar.id
WHERE ar.title = insert_artist
AND al.title = insert_album
LIMIT 1;

SELECT t.id INTO track_check FROM track t
WHERE t.path = insert_path
LIMIT 1;

IF artist_check IS NULL THEN
	INSERT INTO artist (title) VALUES (insert_artist);
    SELECT LAST_INSERT_ID() INTO artist_check;
END IF;
IF album_check IS NULL THEN
	INSERT INTO album (title, artist_id) VALUES (insert_album, artist_check);
    SELECT LAST_INSERT_ID() INTO album_check;
END IF;


IF track_check IS NOT NULL THEN
	/* update if track already exists */
	UPDATE track SET title = insert_track, artist_id = artist_check, album_id = album_check, path = insert_path, track_number = insert_track_number, cover = insert_cover, duration = insert_duration, length = insert_length, genre = insert_genre
	WHERE id = track_check;
ELSE
	/* insert new track */
	INSERT INTO track (title, artist_id, album_id, path, track_number, cover, duration, length, genre)
	VALUES (insert_track, artist_check, album_check, insert_path, insert_track_number, insert_cover, insert_duration, insert_length, insert_genre);
END IF;

END;

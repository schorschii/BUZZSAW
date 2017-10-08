CREATE PROCEDURE `MoveTrackInPlaylist`(`id_move` INT, `move` INT) BEGIN

DECLARE prev_playlist_id INT;
DECLARE prev_sequence INT;

SELECT playlist_id INTO prev_playlist_id
FROM playlist_track
WHERE id = id_move
LIMIT 1;
SELECT sequence INTO prev_sequence
FROM playlist_track
WHERE id = id_move
LIMIT 1;

UPDATE playlist_track
SET sequence = sequence - move
WHERE playlist_id = prev_playlist_id
AND sequence = (prev_sequence+move);

UPDATE playlist_track
SET sequence = sequence + move
WHERE id = id_move;

END;

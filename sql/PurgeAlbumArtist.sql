CREATE PROCEDURE `PurgeAlbumArtist`()

BEGIN

/* purge albums with no tracks anymore */
DELETE a FROM album a
JOIN (
SELECT al.id AS 'id' FROM album al
LEFT JOIN track t on t.album_id = al.id
GROUP BY al.id
HAVING count(t.id) = 0
) del ON a.id = del.id;

/* purge artists with no albums anymore */
DELETE a FROM artist a
JOIN (
SELECT ar.id AS 'id' FROM artist ar
LEFT JOIN album al on al.artist_id = ar.id
GROUP BY ar.id
HAVING count(al.id) = 0
) del ON a.id = del.id;

END;

const express = require('express');
const router = express.Router();
const db = require('../../database');

async function getMediaByFilters(conn, limit, offset, mediaType = null, author = null) {
  let query = `SELECT id, media_path, auteur, date_creation 
               FROM publications 
               WHERE media_path IS NOT NULL`;
  const values = [];

  if (mediaType) {
    if (mediaType === 'image') {
      query += ` AND (media_path LIKE '%.jpg' OR media_path LIKE '%.png' OR media_path LIKE '%.gif')`;
    } else if (mediaType === 'video') {
      query += ` AND (media_path LIKE '%.mp4' OR media_path LIKE '%.avi')`;
    } else if (mediaType === 'audio') {
      query += ` AND media_path LIKE '%.mp3'`;
    }
  }

  if (author) {
    query += ` AND auteur LIKE ?`;
    values.push(`%${author}%`);
  }

  query += ` ORDER BY date_creation DESC LIMIT ${limit} OFFSET ${offset}`;

  console.log('Query:', query);
  console.log('Values:', values);

  try {
    const [rows] = await conn.execute(query, values);
    return rows;
  } catch (error) {
    console.error('Error executing query:', error);
    throw error;
  }
}

async function getTotalMediaCount(conn, mediaType = null, author = null) {
  let query = `SELECT COUNT(*) AS total FROM publications WHERE media_path IS NOT NULL`;
  const values = [];

  if (mediaType) {
    if (mediaType === 'image') {
      query += ` AND (media_path LIKE '%.jpg' OR media_path LIKE '%.png' OR media_path LIKE '%.gif')`;
    } else if (mediaType === 'video') {
      query += ` AND (media_path LIKE '%.mp4' OR media_path LIKE '%.avi')`;
    } else if (mediaType === 'audio') {
      query += ` AND media_path LIKE '%.mp3'`;
    }
  }

  if (author) {
    query += ` AND auteur LIKE ?`;
    values.push(`%${author}%`);
  }

  try {
    const [[result]] = await conn.execute(query, values);
    return result.total;
  } catch (error) {
    console.error('Error getting total media count:', error);
    throw error;
  }
}

router.get('/', async (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  const conn = await db.getConnection();

  const mediaType = req.query.media_type || '';
  const author = req.query.author || '';
  const page = parseInt(req.query.page) || 1;
  const limit = 20;
  const offset = (page - 1) * limit;

  try {
    const mediaPaths = await getMediaByFilters(conn, limit, offset, mediaType, author);
    
    const totalMedia = await getTotalMediaCount(conn, mediaType, author);
    const totalPages = Math.ceil(totalMedia / limit);

    res.render('staff/post_gestion', {
      user: req.userData,
      mediaPaths,
      page,
      totalPages,
      mediaType,
      author,
      cssFile: "/src/css/staff.css",
    });
  } catch (error) {
    console.error('Error in /staff/posts route:', error);
    res.status(500).send('Error processing request');
  }
});

module.exports = router;

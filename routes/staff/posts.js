const express = require('express');
const router = express.Router();
const db = require('../../database');

// Fonction pour récupérer les médias en fonction des filtres
async function getMediaByFilters(conn, limit, offset, mediaType = null, author = null) {
  let query = `SELECT id, media_path, auteur, date_creation 
               FROM publications 
               WHERE media_path IS NOT NULL`;
  const values = [];

  // Filtrer par type de média
  if (mediaType) {
    if (mediaType === 'image') {
      query += ` AND (media_path LIKE '%.jpg' OR media_path LIKE '%.png' OR media_path LIKE '%.gif')`;
    } else if (mediaType === 'video') {
      query += ` AND (media_path LIKE '%.mp4' OR media_path LIKE '%.avi')`;
    } else if (mediaType === 'audio') {
      query += ` AND media_path LIKE '%.mp3'`;
    }
  }

  // Filtrer par auteur
  if (author) {
    query += ` AND auteur LIKE ?`;
    values.push(`%${author}%`);
  }

  // Ajouter la pagination sans utiliser de placeholders pour LIMIT et OFFSET
  query += ` ORDER BY date_creation DESC LIMIT ${limit} OFFSET ${offset}`;

  // Affichage de la requête et des valeurs pour déboguer
  console.log('Query:', query);
  console.log('Values:', values);

  try {
    // Exécution de la requête avec les valeurs
    const [rows] = await conn.execute(query, values);
    return rows;
  } catch (error) {
    // Gestion des erreurs
    console.error('Error executing query:', error);
    throw error;
  }
}

// Fonction pour récupérer le nombre total de médias
async function getTotalMediaCount(conn, mediaType = null, author = null) {
  let query = `SELECT COUNT(*) AS total FROM publications WHERE media_path IS NOT NULL`;
  const values = [];

  // Filtrer par type de média
  if (mediaType) {
    if (mediaType === 'image') {
      query += ` AND (media_path LIKE '%.jpg' OR media_path LIKE '%.png' OR media_path LIKE '%.gif')`;
    } else if (mediaType === 'video') {
      query += ` AND (media_path LIKE '%.mp4' OR media_path LIKE '%.avi')`;
    } else if (mediaType === 'audio') {
      query += ` AND media_path LIKE '%.mp3'`;
    }
  }

  // Filtrer par auteur
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

// Route de gestion des publications
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
    // Récupérer les médias en fonction des filtres
    const mediaPaths = await getMediaByFilters(conn, limit, offset, mediaType, author);
    
    // Récupérer le nombre total de médias
    const totalMedia = await getTotalMediaCount(conn, mediaType, author);
    const totalPages = Math.ceil(totalMedia / limit);

    // Rendre la vue avec les données récupérées
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

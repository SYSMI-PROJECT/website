const express = require('express');
const router = express.Router();
const db = require('../../database'); // Adapte le chemin si nécessaire

// Affichage du plan d'action
router.get('/', async (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.redirect('/');
  }

  let conn;
  try {
    // Connexion à la base de données
    conn = await db.getConnection();

    // Récupérer les tâches
    const [tasks] = await conn.execute('SELECT * FROM plan_action ORDER BY id DESC');

    // Rendu de la vue avec les données
    res.render('staff/tasks', {
      user: req.userData,
      tasks,
      cssFile: '/src/css/plan_action.css', // Adapte si nécessaire
    });
  } catch (error) {
    console.error('Erreur lors de la récupération des tâches :', error);
    res.status(500).send('Erreur interne du serveur');
  } finally {
    // Libération de la connexion
    if (conn) conn.release();
  }
});

// Ajout d'une tâche
router.post('/add', async (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.sendStatus(403); // Interdit si l'utilisateur n'est pas un staff
  }

  const { tache, responsable, echeance } = req.body;
  let conn;

  try {
    conn = await db.getConnection();

    // Insertion d'une nouvelle tâche dans la base de données
    await conn.execute(
      'INSERT INTO plan_action (tache, responsable, echeance) VALUES (?, ?, ?)',
      [tache, responsable, echeance]
    );

    res.sendStatus(200); // Succès
  } catch (error) {
    console.error('Erreur lors de l\'ajout de la tâche :', error);
    res.status(500).send('Erreur interne du serveur');
  } finally {
    // Libération de la connexion
    if (conn) conn.release();
  }
});

// Suppression d'une tâche
router.delete('/delete/:id', async (req, res) => {
  if (!req.userData || req.userData.role !== 'staff') {
    return res.sendStatus(403); // Interdit si l'utilisateur n'est pas un staff
  }

  const { id } = req.params;
  let conn;

  try {
    conn = await db.getConnection();

    // Suppression de la tâche de la base de données
    await conn.execute('DELETE FROM plan_action WHERE id = ?', [id]);

    res.sendStatus(200); // Succès
  } catch (error) {
    console.error('Erreur lors de la suppression de la tâche :', error);
    res.status(500).send('Erreur interne du serveur');
  } finally {
    // Libération de la connexion
    if (conn) conn.release();
  }
});

module.exports = router;

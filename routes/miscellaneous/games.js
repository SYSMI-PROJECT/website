const express = require('express');
const router = express.Router();
const UsersData = require('../../middleware/UsersData');

// Liste des mini-jeux
router.get('/games', async (req, res) => {
  try {
    const gamesRes = [
      {
        nom: 'pacman',
        description: 'Course folle avec Mario et ses amis.',
        image: 'https://images7.alphacoders.com/503/thumb-1920-503083.png',
        category: 'rétro'
      },
      {
        nom: 'snake',
        description: 'Course folle avec Mario et ses amis.',
        image: 'https://wallpapers.com/images/hd/snake-game-1280-x-720-background-ecf2va39zdslaydt.jpg',
        category: 'rétro'
      },
      {
        nom: 'Ping Pong',
        description: 'Un jeu classique de ping-pong.',
        image: 'https://s3.amazonaws.com/gs.apps.screenshots/00000138-c4eb-cab1-9637-f6fcc971d29c.png',
        category: 'rétro'
      },
      {
        nom: 'Tetris',
        description: 'Le jeu classique de puzzle.',
        image: 'https://some-url.com/tetris.png',
        category: 'rétro'
      },
      {
        nom: 'bomberman',
        description: 'Le jeu classique de puzzle.',
        image: 'https://images.launchbox-app.com/d73c1ad8-4485-43b5-b4cf-0fc5f3e07f00.png',
        category: 'rétro'
      }
    ];

    res.render('miscellaneous/games', {
      cssFile: '/src/css/politique.css',
      titre: 'Mini-jeux',
      games: gamesRes,
      user: req.session?.userId || null
    });
  } catch (err) {
    console.error('Erreur dans /games :', err);
    res.status(500).send('Erreur serveur');
  }
});

// Page d’un jeu spécifique (accessible à tous)
router.get('/games/:category/:game', UsersData, (req, res) => {
  const { category, game } = req.params;
  const templatePath = `miscellaneous/games/${category}/${game}`;

  res.render(templatePath, {
    cssFile: `/src/css/games/${category}/${game}.css`,
    titre: `${game.charAt(0).toUpperCase() + game.slice(1)} (${category})`,
    user: req.userData || null
  });
});

module.exports = router;

const express = require('express');
const session = require('express-session');
const mysql = require('mysql2/promise');
const path = require('path');
require('dotenv').config();
const cookieParser = require('cookie-parser');
const logger = require('./logger');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());

app.use(session({
  secret: process.env.SESSION_SECRET || 'secretKey',
  resave: false,
  saveUninitialized: false,
}));

app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'public/uploads')));
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

const db = require('./database');
const checkStayConnected = require('./middleware/checkStayConnected');
const sessionUser = require('./middleware/UsersData');
const getUserProfileImage = require('./middleware/profil');
const getTheme = require('./middleware/get_theme');

app.use(checkStayConnected);
app.use(sessionUser);
app.use(getUserProfileImage);
app.use(getTheme);

const authRoutes = require('./routes/auth');
const miscRoutes = require('./routes/miscellaneous');
const settingsRoute = require('./routes/forms');
const uploadRoutes = require('./routes/upload');
const staffRoutes = require('./routes/staff');

app.use('/auth', authRoutes);
app.use('/settings', settingsRoute);
app.use('/upload', uploadRoutes);
app.use('/staff', staffRoutes);

app.get('/login', (req, res) => {
  const stayConnected = req.cookies.stay_connected ? true : false;
  res.render('auth/login', { stayConnected });
});

app.get('/register', (req, res) => {
  res.render('auth/register');
});

app.get('/', async (req, res) => {
  const conn = await db.getConnection();

  let amis = [], nbDemandes = 0, image_content = null, prenom = '', role = '', etoile = '';
  const user = req.userData;

  if (user) {
    const userId = user.id;

    try {
      logger.database("Executing the friends' request..");

      const [rows] = await conn.execute(
        `SELECT u.id, u.prenom, u.photo_profil
         FROM utilisateur u
         JOIN relation r ON (r.demandeur = u.id OR r.receveur = u.id)
         WHERE (r.demandeur = ? OR r.receveur = ?)
         AND r.statut = 1
         AND u.id != ?`,
        [userId, userId, userId]
      );

      amis = rows.map(ami => ({
        id: ami.id,
        prenom: ami.prenom,
        photo_profil: ami.photo_profil || null
      }));

      prenom = user.prenom;
      role = user.role;
      etoile = user.etoile ?? 0;

      logger.success("Friend request completed!");

      logger.database("Recovering profile picture..");

      image_content = user.photo_profil || null;

      logger.database("Retrieving notifications (requests).");

      const [notifRes] = await conn.execute(
        'SELECT COUNT(*) as count FROM relation WHERE receveur = ? AND statut = 0',
        [userId]
      );

      nbDemandes = notifRes[0].count;

      logger.success("All SQL queries are completed!");
    } catch (error) {
      console.error("Error while executing SQL queries:", error);
    }
  }

  res.render('index', {
    isUserLoggedIn: !!user,
    amis,
    nbDemandes,
    image_content: image_content,
    prenom,
    etoile,
    role,
    cssFile: res.locals.cssFile,
    user_id: user ? user.id : null,
    user,
    userData: {
      photo_profil: image_content
    }
  });
});

app.get('/logout', (req, res) => {
  res.clearCookie('stay_connected');
  req.session.destroy(() => {
    res.redirect('/');
  });
});

app.use('/', miscRoutes);

app.listen(PORT, () => {
  logger.success(`✅ Serveur started : http://localhost:${PORT}`);
});
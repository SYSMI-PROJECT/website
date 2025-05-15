require('dotenv').config();
const mysql = require('mysql2/promise');
const logger = require('./logger');

const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  port: process.env.DB_PORT || 3306,
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || 'password',
  database: process.env.DB_NAME || 'db',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});
  
logger.database("Database connection successful!");

module.exports = pool;

/* eslint-disable import/default */
import React from 'react';
import ReactDOM from 'react-dom/client';

import App from './App';

import './index.scss';

const container = document.getElementById('root');

if (!container) {
  throw new Error('Root element not found. Make sure you have a <div id="root"></div> in your HTML.');
}

const root = ReactDOM.createRoot(container);
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
);

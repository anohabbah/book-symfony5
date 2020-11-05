// eslint-disable-next-line no-unused-vars
import React, { h, render } from 'preact';
import { Router, Link } from 'preact-router';

import '../assets/css/app.scss';

import Home from './pages/home';
import Conferences from './pages/conferences';

function App() {
  return (
    <div>
      <header className="header">
        <nav className="navbar navbar-light bg-light">
          <div className="container">
            <Link className="navbar-brand mr-4 pr-2" href="/">
              <span role="img" aria-label="Book">&#128217;</span>
              Guestbook
            </Link>
          </div>
        </nav>

        <nav className="bg-light border-bottom text-center">
          <Link className="nav-conference" href="/conferences/amsterdam-2019">Amsterdam 2019</Link>
        </nav>
      </header>

      <Router>
        <Home path="/" />
        <Conferences path="/conferences/:slug" />
      </Router>
    </div>
  );
}

render(<App />, document.getElementById('app'));

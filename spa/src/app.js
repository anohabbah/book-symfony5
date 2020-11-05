// eslint-disable-next-line no-unused-vars
import React, { h, render } from 'preact';
import { Router, Link } from 'preact-router';
import { useState, useEffect } from 'preact/hooks';

import '../assets/css/app.scss';

import { findConferences } from './api/api';
import Home from './pages/home';
import Conferences from './pages/conferences';

function App() {
  const [conferences, setConferences] = useState(null);

  useEffect(() => {
    findConferences().then((conf) => setConferences(conf));
  }, []);

  if (conferences === null) {
    return <div className="text-center pt-5">Loading...</div>;
  }

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
          { conferences.map((conference) => (
            <Link className="nav-conference" href={`/conferences/${conference.slug}`}>
              {`${conference.city} ${conference.year}`}
            </Link>
          )) }
        </nav>
      </header>

      <Router>
        <Home path="/" conferences={conferences} />
        <Conferences path="/conferences/:slug" conferences={conferences} />
      </Router>
    </div>
  );
}

render(<App />, document.getElementById('app'));

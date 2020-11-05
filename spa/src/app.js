import React, { h, render } from "preact";
import { Router, Link } from 'preact-router';

import Home from './pages/home';
import Conferences from './pages/conferences';

function App() {
    return (
        <div>
            <header>
                <Link href="/">Home</Link>
                <br/>
                <Link href="/conferences/amsterdam-2019">Amsterdam 2019</Link>
            </header>

            <Router>
                <Home path="/" />
                <Conferences path="/conferences/:slug" />
            </Router>
        </div>
    )
}

render(<App />, document.getElementById('app'))

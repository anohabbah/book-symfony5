// eslint-disable-next-line no-unused-vars
import React, { h } from 'preact';
import { Link } from 'preact-router';
import PropTypes from 'prop-types';

export default function Home({ conferences }) {
  if (!conferences) return <div className="p-3 text-center">No conferences yet</div>;

  return (
    <div className="p-3">
      {conferences.map((conference) => (
        <div className="card border shadow-sm lift mb-3">
          <div className="card-body">
            <div className="card-header">
              <h4 className="font-weight-light">
                {`${conference.city} ${conference.year}`}
              </h4>
            </div>

            <Link className="btn btn-blue btn-sm stretched-link" href={`/conferences/${conference.slug}`}>View</Link>
          </div>
        </div>
      ))}
    </div>
  );
}

Home.propTypes = {
  conferences: PropTypes.instanceOf(Array).isRequired,
};

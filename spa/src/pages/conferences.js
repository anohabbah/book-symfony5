// eslint-disable-next-line no-unused-vars
import React, { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import PropTypes from 'prop-types';

import { findComments } from '../api/api';

function Comment({ comments }) {
  if (comments !== null && comments.length === 0) {
    return <div className="text-center pt-4">No comments yet</div>;
  }

  if (!comments) {
    return <div className="text-center pt-4">Loading...</div>;
  }
  return (
    <div className="pt-4">
      {comments.map((comment) => (
        <div className="shadow border rounded-lg p-3 mb-4">
          <div className="comment-img mr-3">
            {!comment.photoFilename ? '' : (
            // eslint-disable-next-line no-undef
              <a href={`${ENV_API_ENDPOINT}uploads/photos/${comment.photoFilename}`} target="_blank" rel="noreferrer">
                {/* eslint-disable-next-line no-undef */}
                <img src={`${ENV_API_ENDPOINT}uploads/photos/${comment.photoFilename}`} alt={comment.photoFilename} />
              </a>
            )}
          </div>
          <h5 className="font-weight-light mt-3
                  mb-0"
          >
            {comment.author}
          </h5>
          <div className="comment-text">{comment.text}</div>
        </div>
      ))}
    </div>
  );
}

Comment.propTypes = {
  comments: PropTypes.instanceOf(Array).isRequired,
};

export default function Conferences({ conferences, slug }) {
  const conference = conferences.find((item) => item.slug === slug);

  const [comments, setComments] = useState(null);

  useEffect(() => {
    findComments(conference).then((apiComments) => setComments(apiComments));
  }, [slug]);
  return (
    <div className="p-3">
      <h4>
        {`${conference.city} ${conference.year}`}
      </h4>
      <Comment comments={comments} />
    </div>
  );
}

Conferences.propTypes = {
  conferences: PropTypes.instanceOf(Array).isRequired,
  slug: PropTypes.string.isRequired,
};

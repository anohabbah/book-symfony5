function fetchCollection(path) {
  // eslint-disable-next-line no-undef
  return fetch(ENV_API_ENDPOINT + path)
    .then((res) => res.json())
    .then((json) => json['hydra:member']);
}

export function findConferences() {
  return fetchCollection('api/conferences');
}

export function findComments(conference) {
  return fetchCollection(`api/comments?conference=${conference.id}`);
}

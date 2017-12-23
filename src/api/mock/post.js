
export default function postRule(mockAdapter) {
  mockAdapter.onGet('/test').reply(200, {
    test: 'keke'
  })
}
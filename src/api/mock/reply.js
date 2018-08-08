export default function postRule(mockAdapter) {
  mockAdapter
    .onGet(/articles\/\d+\/comments/)
    .reply(200, {
      reply: [
        {
          user: {
            id: 139,
            name: '三木31'
          },
          comment: '6666666222',
          create_at: '2017-07-28T10:41:25Z',
          floor: 2
        },
        {
          user: {
            id: 139,
            name: '三木31'
          },
          comment: '6666666111',
          create_at: '2017-07-28T10:41:25Z',
          floor: 1
        }
      ],
      total: '2'
    })
    .onPost(/articles\/\d+\/comments/)
    .reply(200, {
      user: {
        id: '125',
        name: 'mike'
      },
      comment: '6666666',
      floor: '2',
      update_at: '2017-07-28T10:41:25Z',
      create_at: '2017-07-28T10:41:25Z'
    });
}

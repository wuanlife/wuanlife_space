export default function postRule(mockAdapter) {
  mockAdapter
    .onGet(/articles\/\d+\/comments/)
    .reply(200, {
      reply: [
        {
          user_id: '139',
          comment: '6666666',
          floor: '2',
          create_at: '2017-07-28T10:41:25Z',
          user_name: '三木31'
        },
        {
          user_id: '139',
          comment: '科科',
          floor: '3',
          create_at: '2017-07-28T10:49:25Z',
          user_name: '梁王'
        }
      ],
      total: '123'
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

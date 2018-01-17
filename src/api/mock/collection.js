export default function collecRule(mockAdaptor) {
  mockAdaptor
    .onGet('/users/192/collections?offset=0&limit=20').reply(200, {
      articles: [{
        id: '192',
        title: '我好烦那个人啊',
        content: '淡定吗，无论如何   保持整洁',
        update_at: '2017-07-28T11:32:19Z',
        create_at: '2017-07-28T11:32:19Z',
        delete: false,
        image_urls: [{
          url: ''
        }, {
          url: ''
        }],
        author: {
          id: 1,
          name: '张三'
        }
      }, {
        id: '192',
        title: '我好烦那个人啊',
        content: '淡定吗，无论如何   保持整洁',
        update_at: '2017-07-28T11:32:19Z',
        create_at: '2017-07-28T11:32:19Z',
        delete: false,
        image_urls: [{
          url: ''
        }, {
          url: ''
        }],
        author: {
          id: 2,
          name: '李四'
        }
      }],
      total: 2
    })
}
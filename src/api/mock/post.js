
export default function postRule(mockAdapter) {
  mockAdapter.onGet('/test').reply(200, {
    test: 'keke'
  })
  .onGet('/articles').reply(200, {
    articles: [
      {
        id: '1',
        title: 'this is title this is title this is title this is title this is title this is title ',
        content: 'this is content this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title ',
        update_at: '2017-07-20T12:50:30.176Z',
        create_at: '2017-07-20T12:50:30.176Z',
        approved: true,
        approved_num: 3,
        collected: true,
        collected_num: 0,
        replied: true,
        replied_num: 0,
        image_urls: [
          {
            url: '1.jpg'
          },
          {
            url: '1.jpg'
          }
        ],
        author: {
          avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
          name: '淘淘',
          id: 1
        }
      },
      {
        id: '2',
        title: 'this is title haha',
        content: 'this is content haha',
        update_at: '2017-07-20T12:50:30.176Z',
        create_at: '2017-07-20T12:50:30.176Z',
        approved: true,
        approved_num: 3,
        collected: true,
        collected_num: 0,
        replied: true,
        replied_num: 0,
        image_urls: [
          {
            url: '1.jpg'
          },
          {
            url: '1.jpg'
          },
          {
            url: '1.jpg'
          }
        ],
        author: {
          avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
          name: '淘淘',
          id: 1
        }
      }
    ],
    au: [
      {
        id: 1,
        name: '淘淘',
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      },
      {
        id: 1,
        name: '淘淘',
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      }
    ],
    total: 200
  })
  .onGet('/user/1/articles?offset=0&limit=20').reply(200, {
    articles: [
      {
        id: '1',
        title: 'this is title this is title this is title this is title this is title this is title ',
        content: 'this is content this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title this is title ',
        update_at: '2017-07-20T12:50:30.176Z',
        create_at: '2017-07-20T12:50:30.176Z',
        approved: true,
        approved_num: 3,
        collected: true,
        collected_num: 0,
        replied: true,
        replied_num: 0,
        image_urls: [
          {
            url: '1.jpg'
          },
          {
            url: '1.jpg'
          }
        ],
        author: {
          avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
          name: '淘淘',
          id: 1
        }
      },
      {
        id: '2',
        title: 'this is title haha',
        content: 'this is content haha',
        update_at: '2017-07-20T12:50:30.176Z',
        create_at: '2017-07-20T12:50:30.176Z',
        approved: true,
        approved_num: 3,
        collected: true,
        collected_num: 0,
        replied: true,
        replied_num: 0,
        image_urls: [
          {
            url: '1.jpg'
          },
          {
            url: '1.jpg'
          },
          {
            url: '1.jpg'
          }
        ],
        author: {
          avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
          name: '淘淘',
          id: 1
        }
      }
    ],
    total: 200
  })
}

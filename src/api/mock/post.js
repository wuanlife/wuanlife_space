
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
          name: '张三',
          id: 1,
          //添加数据：每月发布文章数量
          monthly_posts_num: 21111
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
          name: '李四',
          id: 2,
          //添加数据：每月发布文章数量
          monthly_posts_num: 22222
        }
      },
      {
        id: '3',
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
          name: '王五',
          id: 3,
          //添加数据：每月发布文章数量
          monthly_posts_num: 23333
        }
      },
      {
        id: '4',
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
          name: '赵六',
          id: 4,
          //添加数据：每月发布文章数量
          monthly_posts_num: 24444
        }
      }
    ],
//  au: [
//    {
//      id: 1,
//      name: '淘淘',
//      avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
//      
//    
//    },
//    {
//      id: 1,
//      name: '淘淘',
//      avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
//      //添加数据：每月发布文章数量
//      monthly_posts_num: 23333
//    }
//  ],
//  total: 200
})
}

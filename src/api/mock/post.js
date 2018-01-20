
export default function postRule(mockAdapter) {
  mockAdapter.onGet('/test').reply(200, {
    test: 'keke'
  })
  .onGet('/articles?latest=true&offset=0&limit=20').reply(200, {
    articles: [
      {
        id: '1',
        title: 'this is the first page',
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
        title: 'this is the first page',
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
        monthly_articles_num: 123,
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      }, {
        id: 2,
        name: '杰伦',
        monthly_articles_num: 123,
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      }, {
        id: 3,
        name: '伍佰',
        monthly_articles_num: 123,
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      }
    ],
    total: 2
  })
  .onGet('/posts?latest=true&offset=3&limit=20').reply(200, {
    data: [
      {
        id: '1',
        title: 'this is the 3nd page',
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

          // 添加数据：每月发布文章数量
          monthly_posts_num: 23333,
          name: 'tt',
          id: 1
        }
      },
      {
        id: '2',
        title: 'this is the 3nd page',
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
          // 添加数据：每月发布文章数量
          monthly_posts_num: 23333,
          name: 'aoao',
          id: 1
        }
      }
    ],
    total: 200
  })
  .onGet('/user/192/articles?offset=0&limit=20').reply(200, {
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

          // 添加数据：每月发布文章数量
          monthly_posts_num: 23333,
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
          // 添加数据：每月发布文章数量
          monthly_posts_num: 23333,
          name: '淘淘',
          id: 1
        }
      }
    ],
    total: 2
  })
  .onGet('/posts?latest=true&offset=2&limit=20').reply(200, {
    data: [
      {
        id: '1',
        title: 'this is the 2nd page',
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

          // 添加数据：每月发布文章数量
          monthly_posts_num: 23333,
          name: 'tt',
          id: 1
        }
      },
      {
        id: '2',
        title: 'this is the 2nd page',
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
          // 添加数据：每月发布文章数量
          monthly_posts_num: 23333,
          name: 'aoao',
          id: 1
        }
      }
    ],
    total: 200
  })
  .onGet('/users/192').reply(200, { // 获取个人信息
    id: '1',
    avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100',
    mail: 'taotao@taotao.com',
    name: '淘淘',
    sex: '男',
    birthday: '2011-11-11'
  })
  .onPut('users/1/collections').reply(200, {
    success: '收藏成功'
  })
  .onPost('/articles/1/approval').reply(200, {
    success: '点赞成功'
  })
  .onPut('/users/1').reply(200, {
    success: '资料修改成功'
  })
  .onGet('/qiniu/token').reply(200, {
    uploadToken: 'VwLnFpbml1LmNvbSBodHRwOlwvXC8xODMuMTMxLjcuMTgiXX0='
  })
}

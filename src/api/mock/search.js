
export default function searchRule (mockAdaptor) {
  mockAdaptor
    .onGet('/articles/search?keyword=anything&offset=0&limit=20').reply(200, {
      articles: [
        {
          title: 'this is a search result article',
          content: '提醒一下，当使用路由参数时，例如从 /user/foo 导航到 /user/bar，原来的组件实例会被复用。\
          因为两个路由都渲染同个组件，比起销毁再创建，复用则显得更加高效。\
          不过，这也意味着组件的生命周期钩子不会再被调用。复用组件时，\
          想对路由参数的变化作出响应的话，你可以简单地 watch（监测变化） $route 对象：',
          id: '2',
          update_at: '2017-07-20T12:50:30.176Z',
          create_at: '2017-07-20T12:50:30.176Z',
          image: [
            {
              url: ''
            }, {
              url: ''
            }
          ],
          author: {
            id: '1',
            name: 'mike',
            image: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
          }
        }, {
          title: 'this is a search result article',
          content: '提醒一下，当使用路由参数时，例如从 /user/foo 导航到 /user/bar，原来的组件实例会被复用。\
          因为两个路由都渲染同个组件，比起销毁再创建，复用则显得更加高效。\
          不过，这也意味着组件的生命周期钩子不会再被调用。复用组件时，\
          想对路由参数的变化作出响应的话，你可以简单地 watch（监测变化） $route 对象：',
          id: '2',
          update_at: '2017-07-20T12:50:30.176Z',
          create_at: '2017-07-20T12:50:30.176Z',
          image: [
            {
              url: ''
            }, {
              url: ''
            }
          ],
          author: {
            id: '1',
            name: 'mike',
            image: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
          }
        }
      ],
      total: 2
    })
    .onGet('/users/search?keyword=anything&offset=0&limit=20').reply(200, {
      users: [
        {
          id: '1',
          name: '欧阳娜娜',
          image: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
        }, {
          id: '1',
          name: 'mike solomen',
          image: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
        }, {
          id: '1',
          name: 'mike',
          image: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
        }
      ]
    })
}

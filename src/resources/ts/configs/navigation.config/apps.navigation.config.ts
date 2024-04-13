import type { NavigationTree } from '@/@types/navigation';
import {
  NAV_ITEM_TYPE_ITEM,
  NAV_ITEM_TYPE_TITLE,
} from '@/constants/navigation.constant';
import { ADMIN, USER } from '@/constants/roles.constant';
import { APP_PREFIX_PATH } from '@/constants/route.constant';

const appsNavigationConfig: NavigationTree[] = [
  {
    key: 'apps',
    path: '',
    title: 'Menu',
    translateKey: 'nav.apps',
    icon: '',
    type: NAV_ITEM_TYPE_TITLE,
    authority: [ADMIN, USER],
    subMenu: [
      {
        key: 'appsSales.dashboard',
        path: `${APP_PREFIX_PATH}/sales/dashboard`,
        title: 'Dashboard',
        translateKey: 'nav.appsSales.dashboard',
        icon: 'dashboard',
        type: NAV_ITEM_TYPE_ITEM,
        authority: [ADMIN, USER],
        subMenu: [],
      },
      {
        key: '',
        path: ``,
        title: 'Products',
        translateKey: 'nav.appsSales.products',
        icon: 'product',
        type: NAV_ITEM_TYPE_ITEM,
        authority: [ADMIN, USER],
        subMenu: [
          {
            key: 'appsSales.productList',
            path: `${APP_PREFIX_PATH}/sales/product-list`,
            title: 'Product List',
            translateKey: 'nav.appsSales.productList',
            icon: '',
            type: NAV_ITEM_TYPE_ITEM,
            authority: [ADMIN, USER],
            subMenu: [],
          },
          {
            key: 'appsSales.productEdit',
            path: `${APP_PREFIX_PATH}/sales/product-edit/12`,
            title: 'Product Edit',
            translateKey: 'nav.appsSales.productEdit',
            icon: '',
            type: NAV_ITEM_TYPE_ITEM,
            authority: [ADMIN, USER],
            subMenu: [],
          },
          {
            key: 'appsSales.productNew',
            path: `${APP_PREFIX_PATH}/sales/product-new`,
            title: 'New Product',
            translateKey: 'nav.appsSales.productNew',
            icon: '',
            type: NAV_ITEM_TYPE_ITEM,
            authority: [ADMIN, USER],
            subMenu: [],
          },
        ],
      },
      {
        key: '',
        path: ``,
        title: 'Orders',
        translateKey: 'nav.appsSales.orders',
        icon: 'order',
        type: NAV_ITEM_TYPE_ITEM,
        authority: [ADMIN, USER],
        subMenu: [
          {
            key: 'appsSales.orderList',
            path: `${APP_PREFIX_PATH}/sales/order-list`,
            title: 'Order List',
            translateKey: 'nav.appsSales.orderList',
            icon: '',
            type: NAV_ITEM_TYPE_ITEM,
            authority: [ADMIN, USER],
            subMenu: [],
          },
          {
            key: 'appsSales.orderDetails',
            path: `${APP_PREFIX_PATH}/sales/order-details/95954`,
            title: 'Order Details',
            translateKey: 'nav.appsSales.orderDetails',
            icon: '',
            type: NAV_ITEM_TYPE_ITEM,
            authority: [ADMIN, USER],
            subMenu: [],
          },
        ],
      },
    ],
  },
];

export default appsNavigationConfig;

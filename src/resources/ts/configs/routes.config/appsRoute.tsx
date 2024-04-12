import { lazy } from 'react';
import { APP_PREFIX_PATH } from '@/constants/route.constant';
import { ADMIN, USER } from '@/constants/roles.constant';
import type { Routes } from '@/@types/routes';

const appsRoute: Routes = [
  {
    key: 'appsSales.dashboard',
    path: `${APP_PREFIX_PATH}/sales/dashboard`,
    component: lazy(() => import('@/views/sales/SalesDashboard')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsSales.productList',
    path: `${APP_PREFIX_PATH}/sales/product-list`,
    component: lazy(() => import('@/views/sales/ProductList')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsSales.productEdit',
    path: `${APP_PREFIX_PATH}/sales/product-edit/:productId`,
    component: lazy(() => import('@/views/sales/ProductEdit')),
    authority: [ADMIN, USER],
    meta: {
      header: 'Edit Product',
    },
  },
  {
    key: 'appsSales.productNew',
    path: `${APP_PREFIX_PATH}/sales/product-new`,
    component: lazy(() => import('@/views/sales/ProductNew')),
    authority: [ADMIN, USER],
    meta: {
      header: 'Add New Product',
    },
  },
  {
    key: 'appsSales.orderList',
    path: `${APP_PREFIX_PATH}/sales/order-list`,
    component: lazy(() => import('@/views/sales/OrderList')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsSales.orderDetails',
    path: `${APP_PREFIX_PATH}/sales/order-details/:orderId`,
    component: lazy(() => import('@/views/sales/OrderDetails')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsknowledgeBase.helpCenter',
    path: `${APP_PREFIX_PATH}/knowledge-base/help-center`,
    component: lazy(() => import('@/views/knowledge-base/HelpCenter')),
    authority: [ADMIN, USER],
    meta: {
      pageContainerType: 'gutterless',
    },
  },
  {
    key: 'appsknowledgeBase.article',
    path: `${APP_PREFIX_PATH}/knowledge-base/article`,
    component: lazy(() => import('@/views/knowledge-base/Article')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsknowledgeBase.manageArticles',
    path: `${APP_PREFIX_PATH}/knowledge-base/manage-articles`,
    component: lazy(() => import('@/views/knowledge-base/ManageArticles')),
    authority: [ADMIN, USER],
    meta: {
      header: 'Manage Articles',
      extraHeader: lazy(
        () =>
          import(
            '@/views/knowledge-base/ManageArticles/components/PanelHeader'
          ),
      ),
      headerContainer: true,
    },
  },
  {
    key: 'appsknowledgeBase.editArticle',
    path: `${APP_PREFIX_PATH}/knowledge-base/edit-article`,
    component: lazy(() => import('@/views/knowledge-base/EditArticle')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsAccount.settings',
    path: `${APP_PREFIX_PATH}/account/settings/:tab`,
    component: lazy(() => import('@/views/account/Settings')),
    authority: [ADMIN, USER],
    meta: {
      header: 'Settings',
      headerContainer: true,
    },
  },
  {
    key: 'appsAccount.invoice',
    path: `${APP_PREFIX_PATH}/account/invoice/:id`,
    component: lazy(() => import('@/views/account/Invoice')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsAccount.activityLog',
    path: `${APP_PREFIX_PATH}/account/activity-log`,
    component: lazy(() => import('@/views/account/ActivityLog')),
    authority: [ADMIN, USER],
  },
  {
    key: 'appsAccount.kycForm',
    path: `${APP_PREFIX_PATH}/account/kyc-form`,
    component: lazy(() => import('@/views/account/KycForm')),
    authority: [ADMIN, USER],
  },
];

export default appsRoute;

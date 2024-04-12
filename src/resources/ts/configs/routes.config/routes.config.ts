import authRoute from './authRoute';
import appsRoute from './appsRoute';
import uiComponentsRoute from './uiComponentsRoute';
import pagesRoute from './pagesRoute';
import docsRoute from './docsRoute';
import type { Routes } from '@/@types/routes';

export const publicRoutes: Routes = [...authRoute];

export const protectedRoutes: Routes = [
  ...appsRoute,
  ...uiComponentsRoute,
  ...pagesRoute,
  ...docsRoute,
];

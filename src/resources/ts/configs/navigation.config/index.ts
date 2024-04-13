import appsNavigationConfig from './apps.navigation.config';
import docNavigationConfig from './doc.navigation.config';
import type { NavigationTree } from '@/@types/navigation';
import uiComponentNavigationConfig from './ui-components.navigation.config';

const navigationConfig: NavigationTree[] = [
  ...appsNavigationConfig,

  // hide it for now
  ...uiComponentNavigationConfig,

  ...docNavigationConfig,
];

export default navigationConfig;

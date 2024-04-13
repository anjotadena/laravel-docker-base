import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';

import type { NavigationTree } from '@/@types/navigation';
import { Direction, NavMode } from '@/@types/theme';
import AuthorityCheck from '@/components/shared/AuthorityCheck';
import Menu from '@/components/ui/Menu';
import { themeConfig } from '@/configs/theme.config';
import useMenuActive from '@/utils/hooks/useMenuActive';
import VerticalCollapsedMenuItem from './VerticalCollapsedMenuItem';
import VerticalSingleMenuItem from './VerticalSingleMenuItem';

export interface VerticalMenuContentProps {
  navMode: NavMode;
  collapsed?: boolean;
  routeKey: string;
  navigationTree?: NavigationTree[];
  userAuthority: string[];
  onMenuItemClick?: () => void;
  direction?: Direction;
}

const { MenuGroup } = Menu;

const VerticalMenuContent = (props: VerticalMenuContentProps) => {
  const {
    navMode = themeConfig.navMode,
    collapsed,
    routeKey,
    navigationTree = [],
    userAuthority = [],
    onMenuItemClick,
    direction = themeConfig.direction,
  } = props;

  const { t } = useTranslation();

  const [defaulExpandKey, setDefaulExpandKey] = useState<string[]>([]);

  const { activedRoute } = useMenuActive(navigationTree, routeKey);

  useEffect(() => {
    if (defaulExpandKey.length === 0 && activedRoute?.parentKey) {
      setDefaulExpandKey([activedRoute?.parentKey]);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [activedRoute?.parentKey]);

  const getNavItem = (nav: NavigationTree) => {
    if (nav.subMenu.length === 0) {
      return <MenuGroup label={nav.title} />;
    }

    return (
      <AuthorityCheck
        key={nav.key}
        userAuthority={userAuthority}
        authority={nav.authority}
      >
        <MenuGroup label={t(nav.translateKey) || nav.title}>
          {nav.subMenu.map((subNav) =>
            subNav.subMenu.length > 0 ? (
              <VerticalCollapsedMenuItem
                key={subNav.key}
                nav={subNav}
                sideCollapsed={collapsed}
                userAuthority={userAuthority}
                direction={direction}
                onLinkClick={onMenuItemClick}
              />
            ) : (
              <VerticalSingleMenuItem
                key={subNav.key}
                nav={subNav}
                sideCollapsed={collapsed}
                userAuthority={userAuthority}
                direction={direction}
                onLinkClick={onMenuItemClick}
              />
            ),
          )}
        </MenuGroup>
      </AuthorityCheck>
    );
  };

  return (
    <Menu
      className="px-4 pb-4"
      variant={navMode}
      sideCollapsed={collapsed}
      defaultActiveKeys={activedRoute?.key ? [activedRoute.key] : []}
      defaultExpandedKeys={defaulExpandKey}
    >
      {navigationTree.map((nav) => getNavItem(nav))}
    </Menu>
  );
};

export default VerticalMenuContent;

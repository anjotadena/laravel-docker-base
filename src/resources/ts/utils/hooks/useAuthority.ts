import { useMemo } from 'react';
import isEmpty from 'lodash/isEmpty';

function useAuthority(
  userAuthority: string[] = [],
  authority: string[] = [],
  emptyCheck = false,
) {
  const roleMatched = useMemo(
    () => authority.some((role) => userAuthority.includes(role)),
    [authority, userAuthority],
  );

  if (
    isEmpty(authority) ||
    isEmpty(userAuthority) ||
    typeof authority === 'undefined'
  ) {
    return !emptyCheck;
  }

  return roleMatched;
}

export default useAuthority;

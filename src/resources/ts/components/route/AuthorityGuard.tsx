import { PropsWithChildren } from 'react';
import { Navigate } from 'react-router-dom';

import useAuthority from '@/utils/hooks/useAuthority';

type AuthorityGuardProps = PropsWithChildren<{
  userAuthority?: string[];
  authority?: string[];
}>;

/**
 * Function to guard authority access for specific roles.
 *
 * @param {AuthorityGuardProps} props - Object containing userAuthority, authority, and children
 * @return {JSX.Element} The children if role is matched, otherwise a redirect to access denied page
 */
const AuthorityGuard = (props: AuthorityGuardProps) => {
  const { userAuthority = [], authority = [], children } = props;

  const roleMatched = useAuthority(userAuthority, authority);

  return <>{roleMatched ? children : <Navigate to="/access-denied" />}</>;
};

export default AuthorityGuard;

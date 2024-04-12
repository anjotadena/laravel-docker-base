import View from '@/views';

import Simple from './Simple';

const AuthLayout = () => {
  return (
    <div className="app-layout-blank flex flex-auto flex-col h-[100vh]">
      <Simple>
        <View />
      </Simple>
    </div>
  );
};

export default AuthLayout;

import { Provider } from 'react-redux';
import { BrowserRouter } from 'react-router-dom';
import { PersistGate } from 'redux-persist/integration/react';

import Theme from '@/components/template/Theme';
import Layout from '@/components/layouts';
import appConfig from '@/configs/app.config';

import store, { persistor } from './store';
import mockServer from './mock';
import './locales';

const environment = "development";

if (appConfig.enableMock) {
  mockServer({ environment });
}
// Generate unit test
// 
const App = () => (
  <Provider store={store}>
    <PersistGate loading={null} persistor={persistor}>
      <BrowserRouter>
        <Theme>
          <Layout />
        </Theme>
      </BrowserRouter>
    </PersistGate>
  </Provider>
);

export default App;
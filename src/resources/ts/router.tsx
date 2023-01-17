import React from "react";
import { createBrowserRouter } from "react-router-dom";
import App from "./components/App";

import GettingStarted from "./pages/GettingStarted";

const router = createBrowserRouter([
    {
        path: "/",
        element: <App />,
        children: [{
            path: "",
            element: <GettingStarted />
        }]
    },
]);

export default router;

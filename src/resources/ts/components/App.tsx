import React from "react";
import { ThemeProvider } from "@mui/material";

import GlobalStyles from "./GlobalStyle";

import theme from '../config/theme';
import MainLayout from "./layouts/Main";

const App = () => {
    return (
        <ThemeProvider theme={theme}>
            <GlobalStyles />
            <MainLayout />
        </ThemeProvider>
    );
};

export default App;

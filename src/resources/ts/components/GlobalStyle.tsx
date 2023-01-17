import React from "react";
import { GlobalStyles as TSSGlobalStyles } from "tss-react";
import { useStyles } from "tss-react/mui";

const GlobalStyles = () => {
    const { theme } = useStyles();

    return (
        <TSSGlobalStyles
            styles={{
                "*": {
                    boxSizing: "border-box",
                    margin: 0,
                    padding: 0,
                },
                html: {
                    height: "100%",
                    width: "100%",
                    fontFamily: "'Noto Sans Vai', sans-serif",
                },
                body: {
                    backgroundColor: "#f4f6f8",
                    height: "100%",
                    width: "100%",
                },
                a: {
                    textDecoration: "none",
                },
                "#app": {
                    height: "100%",
                    width: "100%",
                },
            }}
        />
    );
};

export default GlobalStyles;

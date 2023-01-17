import React from "react";
import { SvgIcon } from "@mui/material";

type ReactLogoProps = {
    width?: number;
    height?: number;
};

const ReactLogo = (props: ReactLogoProps) => (
    <SvgIcon
        xmlns="http://www.w3.org/2000/svg"
        viewBox="-11.5 -10.23174 23 20.46348"
        width={props.width || 30}
        height={props.height || 30}
    >
        <title>React Logo</title>
        <circle cx="0" cy="0" r="2.05" fill="#61dafb" />
        <g stroke="#61dafb" strokeWidth="1" fill="none">
            <ellipse rx="11" ry="4.2" />
            <ellipse rx="11" ry="4.2" transform="rotate(60)" />
            <ellipse rx="11" ry="4.2" transform="rotate(120)" />
        </g>
    </SvgIcon>
);

export default ReactLogo;

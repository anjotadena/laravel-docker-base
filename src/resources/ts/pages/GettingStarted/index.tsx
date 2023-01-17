import React from "react";
import { Button, Typography, Link } from "@mui/material";
import Grid from "@mui/material/Grid";

import DockerLogo from "../../components/icons/DockerLogo";
import LaravelLogo from "../../components/icons/LaravelLogo";
import ReactLogo from "../../components/icons/ReactLogo";
import MaterialUILogo from "../../components/icons/MaterialUILogo";

const GettingStarted = () => {
    return (
        <Grid
            container
            sx={{ height: "100vh" }}
            display="flex"
            justifyContent="center"
            alignItems="center"
            flexDirection="column"
        >
            <Typography variant="h3" mb={1}>
                Laravel with React SPA
            </Typography>
            <Typography mb={2}>
                Basic boilerplate for a fullstack developer like me!
            </Typography>
            <Button size="medium" variant="outlined">
                Getting Started
            </Button>

            <Grid container spacing={2} minHeight={160} maxWidth={250}>
                <Grid
                    xs
                    display="flex"
                    justifyContent="center"
                    alignItems="center"
                    item
                >
                    <Link
                        href="https://www.docker.com"
                        underline="hover"
                        target="_blank"
                    >
                        <DockerLogo height={30} width={30} />
                    </Link>
                </Grid>
                <Grid
                    xs
                    display="flex"
                    justifyContent="center"
                    alignItems="center"
                    item
                >
                    <Link
                        href="https://ww.laravel.com"
                        underline="hover"
                        target="_blank"
                    >
                        <LaravelLogo height={30} width={30} />
                    </Link>
                </Grid>
                <Grid
                    xs
                    item
                    display="flex"
                    justifyContent="center"
                    alignItems="center"
                >
                    <Link
                        href="https://reactjs.org"
                        underline="hover"
                        target="_blank"
                    >
                        <ReactLogo height={30} width={30} />
                    </Link>
                </Grid>
                <Grid
                    xs
                    item
                    display="flex"
                    justifyContent="center"
                    alignItems="center"
                >
                    <Link
                        href="https://mui.com"
                        underline="hover"
                        target="_blank"
                    >
                        <MaterialUILogo height={30} width={30} />
                    </Link>
                </Grid>
            </Grid>
        </Grid>
    );
};

export default GettingStarted;

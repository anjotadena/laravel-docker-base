```jsx
import { useState, useEffect } from 'react'
import { ComposableMap, Geographies, Geography } from 'react-simple-maps'
import { scaleQuantile } from 'd3-scale'
import { csv } from 'd3-fetch'
import type { DSVRowArray } from 'd3-dsv'

const geoUrl = 'https://cdn.jsdelivr.net/npm/us-atlas@3/counties-10m.json'

const ChoroplethQuantile = () => {
    const [data, setData] = useState<DSVRowArray<string>>()

    useEffect(() => {
        // https://www.bls.gov/lau/
        csv('/data/unemployment-by-county-2017.csv').then((counties) => {
            setData(counties)
        })
    }, [])

    if (!data) {
        return null
    }

    const colorScale = scaleQuantile<string>()
        .domain(data.map((d) => {
            const unemploymentRate = Number(d.unemployment_rate);
            return isNaN(unemploymentRate) ? null : unemploymentRate;
          }))
        .range([
            '#ffedea',
            '#ffcec5',
            '#ffad9f',
            '#ff8a75',
            '#ff5533',
            '#e2492d',
            '#be3d26',
            '#9a311f',
            '#782618',
        ])

    return (
        <ComposableMap
            height={200}
            projectionConfig={{ scale: 450 }}
            projection="geoAlbersUsa"
        >
            <Geographies geography={geoUrl}>
                {({ geographies }) =>
                    geographies.map((geo) => {
                        const cur = data.find((s) => s.id === geo.id)
                        return (
                            <Geography
                                key={geo.rsmKey}
                                geography={geo}
                                fill={cur ? colorScale(Number(cur.unemployment_rate)) : '#EEE'}
                            />
                        )
                    })
                }
            </Geographies>
        </ComposableMap>
    )
}

export default ChoroplethQuantile
```

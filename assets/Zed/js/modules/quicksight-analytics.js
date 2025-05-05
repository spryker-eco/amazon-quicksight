/**
 * Copyright (c) 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

const QuickSightEmbedding = require("amazon-quicksight-embedding-sdk");

$(document).ready(function () {
    const embedConsole = async(embedElement, embedUrl) => {
        const {
            createEmbeddingContext,
        } = QuickSightEmbedding;

        const embeddingContext = await createEmbeddingContext({
            onChange: (changeEvent, metadata) => {
                console.log('Context received a change', changeEvent, metadata);
            },
        });

        const frameOptions = {
            url: embedUrl,
            container: embedElement,
            width: '100%',
            onChange: (changeEvent, metadata) => {
                switch (changeEvent.eventName) {
                    case 'FRAME_MOUNTED': {
                        console.log("Experience frame is mounted.");
                        break;
                    }
                    case 'FRAME_LOADED': {
                        console.log("Experience frame is loaded.");
                        break;
                    }
                }
            },
        };

        const contentOptions = {
            onMessage: async (messageEvent, experienceMetadata) => {
                switch (messageEvent.eventName) {
                    case 'ERROR_OCCURRED': {
                        console.log("Embedded experience failed loading.");
                        break;
                    }
                }
            }
        };
        const embeddedConsoleExperience = await embeddingContext.embedConsole(frameOptions, contentOptions);
    };

    const initQuicksight = () => {
        const embedElement = document.getElementById('experience-container');
        const embedUrl = embedElement.dataset.embedUrl;

        embedConsole(embedElement, embedUrl);
    };

    initQuicksight();
});

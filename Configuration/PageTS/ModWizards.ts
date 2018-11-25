mod {
    wizards {
        newContentElement {
            wizardItems {
                plugins {
                    elements {
                        mmimagemap {
                            iconIdentifier = mmimagemap
                            title = LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.wizard_pi1_title
                            description = LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.wizard_pi1_description
                            tt_content_defValues {
                                CType = list
                                list_type = mmimagemap_pi1
                            }
                        }
                    }
                    show := addToList(mmimagemap)
                }
            }
        }
    }
}
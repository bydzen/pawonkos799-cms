import React, { useState, useEffect } from 'react';
import { addFilter } from '@wordpress/hooks';
import { sendPostMessage } from '@Utils/Helpers';

import { __ } from '@wordpress/i18n';
import { useStateValue } from '../../utils/StateProvider';
import { MediaUpload } from '@wordpress/media-utils';

// SCSS.
import './UploadSiteLogo.scss';

function UploadSiteLogo( props ) {
	const { defaultPageBuilder } = props;
	const replaceMediaUpload = () => MediaUpload;
	const [ { site_logo }, dispatch ] = useStateValue();

	addFilter(
		'editor.MediaUpload',
		'core/edit-post/components/media-upload/replace-media-upload',
		replaceMediaUpload
	);

	const [ imgUpdated, setImageUpdated ] = useState( false );

	useEffect( () => {
		let temp_site_logo = site_logo;

		if ( '' === temp_site_logo && '' !== cartflows_wizard.site_logo.url ) {
			temp_site_logo = cartflows_wizard.site_logo;
		}

		updateValues( temp_site_logo );
	}, [] );

	/**
	 * Prepare the selected image array and update it in the preview iframe.
	 *
	 * @param {media} media
	 */
	const onSelectImage = ( media ) => {
		const mediaData = {
			id: media.id,
			url: media.url,
			width: site_logo.width,
		};

		updateValues( mediaData );
	};

	/**
	 * Remove the selected image from the iframe preview.
	 */
	const removeImage = () => {
		updateValues( '' );
	};

	/**
	 * Update the selected image value in the state and on the iframe preview.
	 *
	 * @param {data} data
	 */
	const updateValues = ( data ) => {
		dispatch( {
			status: 'SET_SITE_LOGO',
			site_logo: data,
		} );

		// Change the preview.
		changelogoInPreview( data );
		setImageUpdated( ! imgUpdated );
	};

	/**
	 * Send the data to the iframe preview window using windows messaging feature.
	 *
	 * @param {data} data
	 */
	const changelogoInPreview = ( data ) => {
		if ( '' === data ) {
			sendPostMessage( {
				action: 'clearHeaderLogo',
				data: {
					default_builder: defaultPageBuilder,
					site_logo: [],
				},
			} );
		} else {
			sendPostMessage( {
				action: 'changeHeaderLogo',
				data: {
					default_builder: defaultPageBuilder,
					site_logo: data,
				},
			} );
		}
	};

	console.log( site_logo );

	return (
		<>
			<div className="wcf-options--row">
				<h3 className="wcf-options--heading">
					{ __( 'Customize', 'cartflows' ) }
				</h3>
				<p className="wcf-options--description">
					{ __(
						"Let's customize your new store checkout with your logo and brand color.",
						'cartflows'
					) }
				</p>
			</div>
			<div className="wcf-options--separator"></div>

			<div className="wcf-options--row">
				<MediaUpload
					onSelect={ ( media ) => onSelectImage( media ) }
					allowedTypes={ [ 'image' ] }
					value={ site_logo.id }
					// multiple={ false }
					render={ ( { open } ) => (
						<>
							<div className="wcf-media-upload-wrapper">
								{ '' !== site_logo.url &&
								undefined !== site_logo.url ? (
									<div className="wcf-site-logo-wrapper">
										<div className="wcf-media-upload--selected-image">
											<div className="wcf-media-upload--preview">
												<span
													className="wcf-close-site-logo"
													onClick={ removeImage }
												>
													<svg
														xmlns="http://www.w3.org/2000/svg"
														width="8"
														height="8"
														viewBox="0 0 8 8"
														fill="#333333"
													>
														<path
															d="M8 0.7L7.3 0L4 3.3L0.7 0L0 0.7L3.3 4L0 7.3L0.7 8L4 4.7L7.3 8L8 7.3L4.7 4L8 0.7Z"
															fill="#333333"
														></path>
													</svg>
												</span>
												<img
													src={ site_logo.url }
													alt={
														'wcf-selected-logo-preview'
													}
													className="wcf-selected-image"
													data-logo-data={ JSON.stringify(
														site_logo
													) }
												/>
												<div
													onClick={ open }
													className="wcf-change-logo-action--wrap"
												>
													<div
														className="wcf-change-logo-action--button"
														onClick={ open }
													>
														{ __(
															'Change Logo',
															'cartflows'
														) }
													</div>
												</div>
											</div>
										</div>
									</div>
								) : (
									''
								) }

								{ '' === site_logo ||
								undefined === site_logo.url ? (
									<div>
										<button
											className="wcf-media-upload-button"
											onClick={ open }
										>
											<h5 className="wcf-media-upload--heading">
												{ __(
													'Upload File Here',
													'cartflows'
												) }
											</h5>

											<p className="text-xs text-[#4B5563]">
												{ __(
													'Suggested Dimensions: 180x60 pixels',
													'cartflows'
												) }
											</p>
										</button>
									</div>
								) : (
									''
								) }
								{ '' === site_logo && (
									<div className="wcf-media-upload--no-image">
										<h5 className="wcf-media-upload--heading">
											{ __(
												"Don't have a logo? No problem!",
												'cartflows'
											) }
										</h5>
										<p className="text-sm text-[#4B5563] mt-1">
											{ __(
												'You can upload it later',
												'cartflows'
											) }
										</p>
									</div>
								) }
							</div>
						</>
					) }
				/>
			</div>
		</>
	);
}

export default UploadSiteLogo;

import React, { useState, useEffect } from 'react';
import { useHistory } from 'react-router-dom';
import { RadioGroup } from '@headlessui/react';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../utils/StateProvider';
import Elementor from '@WizardImages/elementor.svg';
import BeaverBuilder from '@WizardImages/beaver-builder.svg';
import Gutenberg from '@WizardImages/block-editor.png';
import otherPageBuilders from '@WizardImages/other.svg';

const mailingLists = [
	{
		id: 1,
		slug: 'gutenberg',
		title: 'Block Builder',
		image: Gutenberg,
	},
	{
		id: 2,
		slug: 'elementor',
		title: 'Elementor',
		image: Elementor,
	},
	{
		id: 3,
		slug: 'beaver-builder',
		title: 'Beaver Builder',
		image: BeaverBuilder,
	},
	{
		id: 5,
		slug: 'other',
		title: 'Other',
		image: otherPageBuilders,
	},
];

function classNames( ...classes ) {
	return classes.filter( Boolean ).join( ' ' );
}

function PageBuilderStep() {
	const [ selectedMailingLists, setSelectedMailingLists ] = useState(
		mailingLists[ 0 ].slug
	);
	const [ { action_button }, dispatch ] = useStateValue();
	const history = useHistory();

	const handleChange = ( value ) => {
		setSelectedMailingLists( value );

		dispatch( {
			status: 'SET_WIZARD_PAGE_BUILDER',
			selected_page_builder: value,
		} );
	};

	useEffect( () => {
		dispatch( {
			status: 'SET_NEXT_STEP',
			action_button: {
				button_text: __( 'Save & Continue', 'cartflows' ),
				button_class: 'install-page-builder-plugins',
			},
		} );

		const installPbPluginsProcess = document.addEventListener(
			'wcf-page-builder-plugins-install-processing',
			function () {
				dispatch( {
					status: 'SET_NEXT_STEP',
					action_button: {
						button_text: __( 'Saving…', 'cartflows' ),
						button_class: 'install-page-builder-plugins is-loading',
					},
				} );

				dispatch( {
					status: 'PROCESSING',
				} );
			},
			false
		);

		const installPbPluginsSuccess = document.addEventListener(
			'wcf-page-builder-plugins-install-success',
			function () {
				// Stop the processing.
				dispatch( {
					status: 'RESET',
				} );

				history.push( {
					pathname: 'index.php',
					search: `?page=cartflow-setup&step=plugin-install`,
				} );
			},
			false
		);

		return () => {
			document.removeEventListener(
				'wcf-page-builder-plugins-install-processing',
				installPbPluginsProcess
			);

			document.removeEventListener(
				'wcf-page-builder-plugins-install-success',
				installPbPluginsSuccess
			);
		};
	}, [] );

	return (
		<div className="wcf-container">
			<div className="wcf-row text-center mt-12">
				<div className="bg-white rounded mx-auto px-11 py-14 drop-shadow-sm">
					<h1 className="wcf-step-heading">
						{ __(
							'Hi there! Tell us which page builder you use.',
							'cartflows'
						) }
					</h1>

					<div className="flex justify-center">
						<RadioGroup
							value={ selectedMailingLists }
							onChange={ handleChange }
						>
							<RadioGroup.Label className="text-base text-[#1F2937] mt-4 mb-10 sm:mb-10 block">
								{ __(
									"CartFlows works with all page builders, so don't worry if your page builder is not in the list. ",
									'cartflows'
								) }
							</RadioGroup.Label>

							<div className="wcf-pb-list-wrapper">
								{ mailingLists.map( ( mailingList ) => (
									<RadioGroup.Option
										key={ mailingList.id }
										value={ mailingList.slug }
										data-key={ mailingList.slug }
										className={ ( { checked, active } ) =>
											classNames(
												'wcf-pb-list--option hover:translate-y-[-1px] hover:shadow-[0px 4px 8px -2px rgb(9 30 66 / 25%), 0px 0px 1px rgb(9 30 66 / 31%)]',
												checked
													? 'border-transparent'
													: 'border-gray-300',
												active ? '' : ''
											)
										}
									>
										{ ( { checked, active } ) => (
											<>
												<div className="flex-auto flex justify-center">
													<div className="text-center">
														<RadioGroup.Description
															as="img"
															className="block text-sm font-normal text-[#4B5563] h-[45%] rounded-full m-5"
															src={
																mailingList.image
															}
														></RadioGroup.Description>
														<RadioGroup.Label
															as="div"
															className="block text-sm font-normal text-[#4B5563] mt-4"
														>
															{
																mailingList.title
															}
														</RadioGroup.Label>
													</div>
												</div>

												<div
													className={ classNames(
														active
															? 'border-2'
															: 'border-2',
														checked
															? 'border-orange-500'
															: 'border-transparent',
														'absolute -inset-px rounded pointer-events-none'
													) }
													aria-hidden="true"
												/>
											</>
										) }
									</RadioGroup.Option>
								) ) }
							</div>
						</RadioGroup>

						<span
							id="wcf-selected-page-builder"
							data-selected-pb={ selectedMailingLists }
						></span>
					</div>

					<div className="mt-[40px] flex justify-center">
						<div
							className={ `wcf-wizard--button ${
								action_button.button_class
									? action_button.button_class
									: ''
							}` }
						>
							{ action_button.button_text }
							<svg
								xmlns="http://www.w3.org/2000/svg"
								className="w-5 mt-0.5 ml-1.5 fill-[#243c5a]"
								fill="none"
								viewBox="0 0 24 24"
								stroke="currentColor"
								strokeWidth={ 2 }
							>
								<path
									strokeLinecap="round"
									strokeLinejoin="round"
									d="M17 8l4 4m0 0l-4 4m4-4H3"
								/>
							</svg>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}

export default PageBuilderStep;

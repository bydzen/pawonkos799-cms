import React, { useState } from 'react';
import reactCSS from 'reactcss';
import './ColorPickerField.scss';
import { __ } from '@wordpress/i18n';

import { SketchPicker } from 'react-color';

function ColorPickerField( props ) {
	const { name, label, value, isActive = true, handleOnchange } = props;

	const [ displayColorPicker, setdisplayColorPicker ] = useState( false );
	const [ color, setColor ] = useState( value );

	const styles = reactCSS( {
		default: {
			color: {
				width: '36px',
				height: '30px',
				background: color,
			},
		},
	} );

	const handleClick = () => {
		setdisplayColorPicker( ( prevValue ) => ! prevValue );
	};
	const handleClose = () => {
		setdisplayColorPicker( false );
	};
	const handleResetColor = () => {
		handleChange( '' );
	};

	const handleChange = ( newcolor ) => {
		if ( newcolor ) {
			setColor( newcolor.hex );
		} else {
			setColor( newcolor );
		}

		// Trigger change
		const changeEvent = new CustomEvent( 'wcf:color:change', {
			bubbles: true,
			detail: {
				e: 'color',
				name: props.name,
				value: newcolor ? newcolor.hex : newcolor,
			},
		} );

		document.dispatchEvent( changeEvent );

		if ( handleOnchange ) {
			handleOnchange( newcolor );
		}
	};
	return (
		<div
			className={ `wcf-field wcf-color-field ${
				! isActive ? 'wcf-hide' : ''
			}` }
		>
			<div className="wcf-field__data">
				{ label && (
					<div className="wcf-field__data--label">
						<label>{ label }</label>
					</div>
				) }
				<div className="wcf-field__data--content">
					<div className="wcf-colorpicker-selector">
						<div
							className="wcf-colorpicker-swatch-wrap"
							onClick={ handleClick }
						>
							<span
								className="wcf-colorpicker-swatch"
								style={ styles.color }
							/>
							<span className="wcf-colorpicker-label">
								Select Color
							</span>
							<input
								type="hidden"
								name={ name }
								value={ color }
							/>
						</div>
						{ color && (
							<span
								className="wcf-colorpicker-reset"
								onClick={ handleResetColor }
								title={ __( 'Reset', 'cartflows' ) }
							>
								<span className="dashicons dashicons-update-alt"></span>
							</span>
						) }
					</div>
					<div className="wcf-color-picker">
						{ displayColorPicker ? (
							<div className="wcf-color-picker-popover">
								<div
									className="wcf-color-picker-cover"
									onClick={ handleClose }
								/>
								<SketchPicker
									name={ name }
									color={ color }
									onChange={ handleChange }
									disableAlpha={ true }
								/>
							</div>
						) : null }
					</div>
				</div>
			</div>
		</div>
	);
}

export default ColorPickerField;

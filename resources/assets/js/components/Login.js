import React, {Component} from 'react';
import ReactDOM from 'react-dom';

import axios from 'axios';
import Input from '@material-ui/core/Input';
import Button from '@material-ui/core/Button';
import Checkbox from '@material-ui/core/Checkbox';
import IconButton from '@material-ui/core/IconButton';
import InputAdornment from '@material-ui/core/InputAdornment';
import LinearProgress from '@material-ui/core/LinearProgress';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Visibility from '@material-ui/icons/Visibility';
import VisibilityOff from '@material-ui/icons/VisibilityOff';
import Fingerprint from '@material-ui/icons/Fingerprint';
import orange from '@material-ui/core/colors/orange';

import logo_img from '../../images/logo.png';

class LoginComponent extends React.Component {
    constructor(props) {
        super(props)
        this.do_login_url = "http://ubiz.local/api/v1/auth/login";
        this.handleLoginClick = this.handleLoginClick.bind(this);
        this.handleEmailChange = this.handleEmailChange.bind(this);
        this.handlePasswordChange = this.handlePasswordChange.bind(this);
        this.handleRememberCheck = this.handleRememberCheck.bind(this);
        this.handleMouseDownPassword = this.handleMouseDownPassword.bind(this);
        this.handleClickShowPassword = this.handleClickShowPassword.bind(this);
        this.state = {
            email: '',
            password: '',
            showPassword: false,
            remember: false,
            linear_progress_show: false,
        }
    }

    handleEmailChange(event) {
        this.setState({email: event.target.value});
    }

    handlePasswordChange(event) {
        this.setState({password: event.target.value});
    }

    handleRememberCheck() {
        this.setState((oldState) => {
            return {
                remember: !oldState.remember,
            };
        });
    }

    handleLoginClick() {
        this.setState({linear_progress_show: true});
        axios.post(this.do_login_url, this.state)
            .then(res => {
                this.setState({linear_progress_show: false});
                if (res.data.success == true) {
                    window.location.href = '/';
                } else {
                    alert(res.data.message);
                }
            });
    }

    handleMouseDownPassword(event) {
        event.preventDefault();
    }

    handleClickShowPassword() {
        this.setState({showPassword: !this.state.showPassword});
    }

    render() {
        let linear_progress_display = "none";
        if (this.state.linear_progress_show == true) {
            linear_progress_display = "block";
        }
        return (
            <div className="login">
                <div className="background">
                    <div className="backgroundImage"></div>
                </div>
                <div className="outer">
                    <div className="middle">
                        <div className="inner">
                            <LinearProgress
                                style={{
                                    color: orange[500],
                                    borderRadius: 0,
                                    display: linear_progress_display,
                                    width: 'calc(100% - 0.5px)'
                                }}
                            />
                            <div className="inner-form">
                                <div className="row margin-bottom-15">
                                    <div className="col-md-3">
                                        <img className='img-logo' src={`.${logo_img}`}/>
                                    </div>
                                    <div className="col-md-9">
                                        <label className="txt-logo">Ubiz</label>
                                    </div>
                                </div>
                                <div className="row margin-bottom-15">
                                    <div className="col-md-12">
                                        <Input
                                            fullWidth
                                            id="adornment-mail"
                                            placeholder="Email"
                                            value={this.state.email}
                                            className="font-size-1-5em"
                                            onChange={this.handleEmailChange}
                                        />
                                    </div>
                                </div>
                                <div className="row margin-bottom-25">
                                    <div className="col-md-12">
                                        <Input
                                            fullWidth
                                            id="adornment-password"
                                            placeholder="Password"
                                            className="font-size-1-5em"
                                            type={this.state.showPassword ? 'text' : 'password'}
                                            fullWidth
                                            value={this.state.password}
                                            onChange={this.handlePasswordChange}
                                            endAdornment={
                                                <InputAdornment position="end">
                                                    <IconButton
                                                        aria-label="Toggle password visibility"
                                                        onClick={this.handleClickShowPassword}
                                                        onMouseDown={this.handleMouseDownPassword}
                                                    >
                                                        {this.state.showPassword ? <VisibilityOff/> : <Visibility/>}
                                                    </IconButton>
                                                </InputAdornment>
                                            }
                                        />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <Button
                                            fullWidth
                                            variant="raised"
                                            color="primary"
                                            className="ubiz-btn"
                                            onClick={this.handleLoginClick}
                                        >
                                            Đăng nhập
                                            <Fingerprint style={{color: "white"}}/>
                                        </Button>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <FormControlLabel
                                            className="font-size-1em"
                                            control={
                                                <Checkbox
                                                    checked={this.state.remember}
                                                    onChange={this.handleRememberCheck}
                                                    value="remember"
                                                />
                                            }
                                            label="Giữ tôi luôn đăng nhập"
                                        />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-12">
                                        <a href="#" className="">Quên mật khẩu ?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

var login_page = document.getElementById('login-page');
if (login_page != null) {
    ReactDOM.render(<LoginComponent/>, login_page);
}
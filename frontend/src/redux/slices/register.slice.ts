/* eslint-disable @typescript-eslint/no-explicit-any */
import {
	RegisterCodePayload,
	RegisterEndPayload,
	RegisterStartPayload,
	RegisterState,
} from "@/types/redux/register";
import { AnyAction, createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import { getSubstring } from "../../utils/functions/getSubstring";
import RegisterService from "../services/register.service";

export const startRegisterAsync = createAsyncThunk<
	number,
	RegisterStartPayload
>("register/start", async ({ name, email, birthDate }) => {
	try {
		const registrationId = await RegisterService.registerStart(
			name,
			email,
			birthDate
		);
		return registrationId;
	} catch (error: any) {
		if (error.response) {
			console.error(error.response.status, error.response.data);
		} else if (error.request) {
			console.error(error.request);
		} else {
			console.error(error.message);
		}
		throw error;
	}
});

export const codeRegisterAsync = createAsyncThunk(
	"register/code",
	async ({ code, registrationId }: RegisterCodePayload): Promise<number> => {
		try {
			const status = await RegisterService.registerCode(
				code,
				registrationId
			);
			return status;
		} catch (error: any) {
			if (error.response) {
				console.error(error.response.status, error.response.data);
			} else if (error.request) {
				console.error(error.request);
			} else {
				console.error(error.message);
			}
			throw error;
		}
	}
);

export const registerAsync = createAsyncThunk(
	"register/signup",
	async ({ password, registrationId }: RegisterEndPayload) => {
		try {
			const status = await RegisterService.register(
				password,
				registrationId
			);
			return status;
		} catch (error: any) {
			if (error.response) {
				console.error(error.response.status, error.response.data);
			} else if (error.request) {
				console.error(error.request);
			} else {
				console.error(error.message);
			}
			throw error;
		}
	}
);

const initialState: RegisterState = {
	registrationId: null,
	loading: false,
	error: null,
};

const handlePending = (state: RegisterState) => {
	state.loading = true;
	state.error = null;
};

const handleRejected = (state: RegisterState, action: AnyAction) => {
	state.loading = false;
	if (action.error) {
		state.error = {
			message: action.error.message,
			status: getSubstring(action.error.message, " "),
		};
	}
};

const registerSlice = createSlice({
	name: "register",
	initialState,
	reducers: {},
	extraReducers: (builder) => {
		builder
			.addCase(startRegisterAsync.pending, handlePending)
			.addCase(startRegisterAsync.fulfilled, (state, action) => {
				state.loading = false;
				state.error = null;
				state.registrationId = action.payload;
			})
			.addCase(startRegisterAsync.rejected, handleRejected)

			.addCase(codeRegisterAsync.pending, handlePending)
			.addCase(codeRegisterAsync.fulfilled, (state) => {
				state.loading = false;
				state.error = null;
			})
			.addCase(codeRegisterAsync.rejected, handleRejected)

			.addCase(registerAsync.pending, handlePending)
			.addCase(registerAsync.fulfilled, (state) => {
				state.loading = false;
				state.error = null;
			})
			.addCase(registerAsync.rejected, handleRejected);
	},
});

export default registerSlice.reducer;

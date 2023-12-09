/* eslint-disable @typescript-eslint/no-explicit-any */
import {
	RegisterCodePayload,
	RegisterEndPayload,
	RegisterStartPayload,
	RegisterState,
} from "@/types/redux/register";
import { AnyAction, createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import { getLastEntry } from "../../utils/functions/getLastEntry";
import RegisterService from "../services/register.service";

export const startRegisterAsync = createAsyncThunk<
	number,
	RegisterStartPayload
>("register/start", async ({ name, email, birth_date }) => {
	try {
		const registrationId = await RegisterService.registerStart(
			name,
			email,
			birth_date
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
			status: getLastEntry(action.error.message, " "),
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

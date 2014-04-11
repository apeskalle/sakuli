/*
* Sakuli - Testing and Monitoring-Tool for Websites and common UIs.
*
* Copyright 2013 - 2014 the original author or authors.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

/**** Exclude this global variables from JSLint Warnings ****/
/* global navigator, window, java, Packages,saveResult,step, $output, _set, _stopOnError, _logExceptionAsFailure,_resolvePath,_include, $sahi_userdata, $guid, $capture, initialize */


/*****************************************************************************************************
* Environment Class
*
* @param optResumeOnException if this parameter is undefined, it will be false.
*****************************************************************************************************/

function Environment(optResumeOnException) {
if (undefined == optResumeOnException) {
optResumeOnException = new Boolean(false);
}
return loadEnvironment(Packages.de.consol.sakuli.loader.BeanLoader.loadEnvironment(optResumeOnException), optResumeOnException);
}

function loadEnvironment(javaObject, resumeOnException) {
var that = {}, update;

/**
* set a new default similarity for the screen capturing methods.
*
* @param similarity double value between 0 and 1, default = 0.8f
* @return this {@link Environment} or NULL on errors.
*/
that.setSimilarity = function (similarity) {
return update(that.javaObject.setSimilarity(similarity));
};

/**
* @return a {@link Region} object from the current focused window
*         or NULL on errors.
*/
that.getRegionFromFocusedWindow = function () {
return loadRegion(that.javaObject.getRegionFromFocusedWindow(), that.resumeOnException);
};

/**
* Takes a screenshot of the current screen and saves it to the overgiven path.
* If there ist just a file name, the screenshot will be saved in your testsuite log folder.
*
* @param pathName "pathname/filname.format" or just "filename.format"<br>
*                 for example "test.png".
*/
that.takeScreenshot = function (pathName) {
return that.javaObject.takeScreenshot(pathName);
};

/**
* Blocks the current testcase execution for x seconds
*
* @param seconds to sleep
* @return this {@link Environment} or NULL on errors.
*/
that.sleep = function (seconds) {
return update(that.javaObject.sleep(seconds));
};

/**
* @return the current content of the clipboard as {@link String} or NULL on errors
*/
that.getClipboard = function () {
return that.javaObject.getClipboard();
};

/**
* sets the String paramter to the system clipboard
*
* @param text as {@link String}
* @return this {@link Environment}.
*/
that.setClipboard = function (text) {
return update(that.javaObject.setClipboard(text));
};

/**
* pastes the current clipboard content into the focused area.
* Will do the same as "STRG + C".
*
* @return this {@link Environment}.
*/
that.pasteClipboard = function () {
return update(that.javaObject.pasteClipboard());
};


/**
* copy the current selected item or text to the clipboard.
* Will do the same as "STRG + V".
*
* @return this {@link Environment}.
*/
that.copyIntoClipboard = function () {
return update(that.javaObject.copyIntoClipboard());
};


/**
* pastes the text at the current position of the focus/carret <br/>using the
* clipboard and strg/ctrl/cmd-v (paste keyboard shortcut)
*
* @param text a string, which might contain unicode characters
* @return this {@link Environment} or NULL on errors.
*/
that.paste = function (text) {
return update(that.javaObject.paste(text));
};


/**
* makes a masked {@link #paste(String)} without any logging.
*
* @param text a string, which might contain unicode characters
* @return this {@link Environment} or NULL on errors.
*/
that.pasteMasked = function (text) {
return update(that.javaObject.pasteMasked(text));
};

/**
* combines {@link #pasteMasked(String)} and {@link #decryptSecret(String)}.
*
* @param text encrypted secret
* @return this {@link Environment} or NULL on errors.
*/
that.pasteAndDecrypt = function (text) {
return update(that.javaObject.pasteAndDecrypt(text));
};

/**
* Enters the given text one character/key after another using keyDown/keyUp.
* <p/>
* About the usable Key constants see documentation of {@link Key}.
* The function use a subset of a US-QWERTY PC keyboard layout to type the text.
* The text is entered at the current position of the focus.
*
* @param text containing characters and/or {@link Key} constants
* @param optModifiers (optional) an String with only {@link Key} constants.
* @return this {@link Environment} or NULL on errors.
*/
that.type = function (text, optModifiers) {
if (undefined == optModifiers) {
return update(that.javaObject.type(text));
}
return update(that.javaObject.type(text, optModifiers));
};

/**
* Enters the given text one character/key after another using keyDown/keyUp.
* The entered text will be masked at the logging.
* <p/>
* About the usable Key constants see documentation of {@link Key}.
* The function use a subset of a US-QWERTY PC keyboard layout to type the text.
* The text is entered at the current position of the focus.
*
* @param text containing characters and/or {@link Key} constants
* @param optModifiers (optional) an String with only {@link Key} constants.
* @return this {@link Environment} or NULL on errors.
*/
that.typeMasked = function (text, optModifiers) {
if (undefined == optModifiers) {
return update(that.javaObject.typeMasked(text));
}
return update(that.javaObject.typeMasked(text, optModifiers));
};


/**
* Decrypt and enters the given text one character/key after another using keyDown/keyUp.
* The entered text will be masked at the logging. For the deatails of the decryption see {@link #decryptSecret(String)}.
* <p/>
* About the usable Key constants see documentation of {@link Key}.
* The function use a subset of a US-QWERTY PC keyboard layout to type the text.
* The text is entered at the current position of the focus.
*
* @param text containing characters and/or {@link Key} constants
* @param optModifiers (optional) an String with only {@link Key} constants.
* @return this {@link Environment} or NULL on errors.
*/
that.typeAndDecrypt = function (text, optModifiers) {
if (undefined == optModifiers) {
return update(that.javaObject.typeAndDecrypt(text))
}
return update(that.javaObject.typeAndDecrypt(text, optModifiers));
};

/**
* Decrypt a encrypted secret and returns the value at runtime.
* The decryption will only work if the encryption and decryption happen on the same physical machine.
* There will be no logging with the decrypted secret during this step.
* <p/>
* To create a encrypted secret see "README.txt".
*
* @param secret encrypted secret as {@link String}
* @return decrypted {@link String}
*/
that.decryptSecret = function (text) {
return that.javaObject.decryptSecret(text);
};

/*********************
* MOUSE WHEEL FUNCTIONS
*********************/
/**
* move the mouse pointer to the given target location and move the
* wheel the given steps down.
*
* @param steps the number of steps
*/
that.mouseWheelDown = function (steps) {
return update(that.javaObject.mouseWheelDown(steps));
};

/**
* move the mouse pointer to the given target location and move the
* wheel the given steps up.
*
* @param steps the number of steps
*/
that.mouseWheelUp = function (steps) {
return update(that.javaObject.mouseWheelUp(steps));
};

/*******************************
* LOGGING FUNCTIONS
*/

/**
* make a error-log over Java backend into the log file.
* This won't stop the execution of the test case.
* The log entries can be configured over the properties "log4.properties"
*
* @param message as a String
*/
that.logError = function (message) {
Packages.de.consol.sakuli.actions.logging.Logger.logError(message);
return that;
};

/**
* make a debug-log over Java backend into the log file.
* The log entries can be configured over the properties "log4.properties"
*
* @param message as a String
*/
that.logWarning = function (message) {
Packages.de.consol.sakuli.actions.logging.Logger.logWarning(message);
return that;
};

/**
* make a info-log over Java backend into the log file.
* The log entries can be configured over the properties "log4.properties"
*
* @param message as a String
*/
that.logInfo = function (message) {
Packages.de.consol.sakuli.actions.logging.Logger.logInfo(message);
return that;
};
/**
* make a debug-log over Java backend into the log file.
* The log entries can be configured over the properties "log4.properties"
*
* @param message as a String
*/
that.logDebug = function (message) {
Packages.de.consol.sakuli.actions.logging.Logger.logDebug(message);
return that;
};


/*****************************************************************************************************
* INTERNAL CLASS FUNCTIONS - NOT REACHABLE IN THE TEST CASE EXECUTION
*****************************************************************************************************/

that.javaObject = javaObject;
that.resumeOnException = new Boolean(resumeOnException);

update = function (updatedJavaObject) {
if (undefined == updatedJavaObject || updatedJavaObject == null) {
return undefined;
}
return new loadEnvironment(updatedJavaObject, that.resumeOnException);
};

return that;
};



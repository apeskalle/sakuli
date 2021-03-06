/*
 * Sakuli - Testing and Monitoring-Tool for Websites and common UIs.
 *
 * Copyright 2013 - 2015 the original author or authors.
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

package org.sakuli.datamodel.state;

import java.util.Arrays;
import java.util.List;

/**
 * Enum which represents the Sahi-Case-Stati in file "sahi_return_codes"
 *
 * @author tschneck
 *         Date: 21.06.13
 */
public enum TestCaseStepState implements SakuliState {
    /**
     * value = 0
     */
    OK(0, "ok"),

    /**
     * value = 1
     */
    WARNING(1, "warning"),

    /**
     * value = 2
     */
    CRITICAL(2, "critical"),

    /**
     * value = 1
     */
    ERRORS(4, "EXCEPTION"),

    /**
     * state before the execution
     */
    INIT(-1, "initialized");

    private final int errorCode;
    private final String stateDescription;

    TestCaseStepState(int i, String stateDescription) {
        this.errorCode = i;
        this.stateDescription = stateDescription;
    }

    @Override
    public int getNagiosErrorCode() {
        if (isOk()) {
            return 0;
        } else if (isWarning()) {
            return 1;
        } else if (isCritical()) {
            return 2;
        }
        return 3;
    }

    public int getErrorCode() {
        return errorCode;
    }

    @Override
    public String getNagiosStateDescription() {
        return stateDescription;
    }

    @Override
    public boolean isOk() {
        return getOkCodes().contains(this);
    }

    @Override
    public boolean isWarning() {
        return getWarningCodes().contains(this);
    }

    @Override
    public boolean isCritical() {
        return getCriticalCodes().contains(this);
    }

    public List<TestCaseStepState> getOkCodes() {
        return Arrays.asList(OK);
    }

    public List<TestCaseStepState> getWarningCodes() {
        return Arrays.asList(WARNING);
    }

    public List<TestCaseStepState> getCriticalCodes() {
        return Arrays.asList(CRITICAL, ERRORS);
    }

    @Override
    public boolean isError() {
        return this.equals(ERRORS);
    }

    @Override
    public boolean isFinishedWithoutErrors() {
        return !Arrays.asList(INIT, ERRORS).contains(this);
    }

}
